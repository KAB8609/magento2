<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout merge model
 */
class Magento_Core_Model_Layout_Merge
{
    /**#@+
     * Available item type names
     */
    const TYPE_PAGE = 'page';
    const TYPE_FRAGMENT = 'fragment';
    /**#@-*/

    /**
     * XPath of handles originally declared in layout updates
     */
    const XPATH_HANDLE_DECLARATION = '/layout[@*]';

    /**
     * @var Magento_Core_Model_Theme
     */
    private $_theme;

    /**
     * @var Magento_Core_Model_Store
     */
    private $_store;

    /**
     * In-memory cache for loaded layout updates
     *
     * @var Magento_Core_Model_Layout_Element
     */
    protected $_layoutUpdatesCache;

    /**
     * Cumulative array of update XML strings
     *
     * @var array
     */
    protected $_updates = array();

    /**
     * Handles used in this update
     *
     * @var array
     */
    protected $_handles = array();

    /**
     * Page handle names sorted by from parent to child
     *
     * @var array
     */
    protected $_pageHandles = array();

    /**
     * Substitution values in structure array('from' => array(), 'to' => array())
     *
     * @var array|null
     */
    protected $_subst = null;

    /**
     * @var Magento_Core_Model_Layout_File_SourceInterface
     */
    private $_fileSource;

    /**
     * @var Magento_Core_Model_Resource_Layout_Update
     */
    private $_resource;

    /**
     * @var Magento_Core_Model_App_State
     */
    private $_appState;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_cache;

    /**
     * Init merge model
     *
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Layout_File_SourceInterface $fileSource,
     * @param Magento_Core_Model_Resource_Layout_Update $resource
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Cache_FrontendInterface $cache
     * @param Magento_Core_Model_Theme $theme Non-injectable theme instance
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Layout_File_SourceInterface $fileSource,
        Magento_Core_Model_Resource_Layout_Update $resource,
        Magento_Core_Model_App_State $appState,
        Magento_Cache_FrontendInterface $cache,
        Magento_Core_Model_Theme $theme = null
    ) {
        $this->_theme = $theme ?: $design->getDesignTheme();
        $this->_store = $storeManager->getStore();
        $this->_fileSource = $fileSource;
        $this->_resource = $resource;
        $this->_appState = $appState;
        $this->_cache = $cache;
    }

    /**
     * Add XML update instruction
     *
     * @param string $update
     * @return Magento_Core_Model_Layout_Merge
     */
    public function addUpdate($update)
    {
        $this->_updates[] = $update;
        return $this;
    }

    /**
     * Get all registered updates as array
     *
     * @return array
     */
    public function asArray()
    {
        return $this->_updates;
    }

    /**
     * Get all registered updates as string
     *
     * @return string
     */
    public function asString()
    {
        return implode('', $this->_updates);
    }

    /**
     * Add handle(s) to update
     *
     * @param array|string $handleName
     * @return Magento_Core_Model_Layout_Merge
     */
    public function addHandle($handleName)
    {
        if (is_array($handleName)) {
            foreach ($handleName as $name) {
                $this->_handles[$name] = 1;
            }
        } else {
            $this->_handles[$handleName] = 1;
        }
        return $this;
    }

    /**
     * Remove handle from update
     *
     * @param string $handleName
     * @return Magento_Core_Model_Layout_Merge
     */
    public function removeHandle($handleName)
    {
        unset($this->_handles[$handleName]);
        return $this;
    }

    /**
     * Get handle names array
     *
     * @return array
     */
    public function getHandles()
    {
        return array_keys($this->_handles);
    }

    /**
     * Add the first existing (declared in layout updates) page handle along with all parents to the update.
     * Return whether any page handles have been added or not.
     *
     * @param array $handlesToTry
     * @return bool
     */
    public function addPageHandles(array $handlesToTry)
    {
        foreach ($handlesToTry as $handleName) {
            if (!$this->pageHandleExists($handleName)) {
                continue;
            }
            $handles = $this->getPageHandleParents($handleName);
            $handles[] = $handleName;

            /* replace existing page handles with the new ones */
            foreach ($this->_pageHandles as $pageHandleName) {
                $this->removeHandle($pageHandleName);
            }
            $this->_pageHandles = $handles;
            $this->addHandle($handles);
            return true;
        }
        return false;
    }

    /**
     * Retrieve the all parent handles ordered from parent to child. The $isPageTypeOnly parameters controls,
     * whether only page type parent relation is processed.
     *
     * @param string $handleName
     * @param bool $isPageTypeOnly
     * @return array
     */
    public function getPageHandleParents($handleName, $isPageTypeOnly = true)
    {
        $result = array();
        $node = $this->_getPageHandleNode($handleName);
        while ($node) {
            $parentItem = $node->getAttribute('parent');
            if (!$parentItem && !$isPageTypeOnly) {
                $parentItem = $node->getAttribute('owner');
            }
            $node = $this->_getPageHandleNode($parentItem);
            if ($node) {
                $result[] = $parentItem;
            }
        }
        return array_reverse($result);
    }

    /**
     * Whether a page handle is declared in the system or not
     *
     * @param string $handleName
     * @return bool
     */
    public function pageHandleExists($handleName)
    {
        return (bool)$this->_getPageHandleNode($handleName);
    }

    /**
     * Get handle xml node by handle name
     *
     * @param string $handleName
     * @return Magento_Core_Model_Layout_Element|null
     */
    protected function _getPageHandleNode($handleName)
    {
        /* quick validation for non-existing page types */
        if (!$handleName) {
            return null;
        }
        $handles = $this->getFileLayoutUpdatesXml()->xpath("handle[@id='$handleName']");
        if (empty($handles)) {
            return null;
        }
        $condition = '@type="' . self::TYPE_PAGE . '" or @type="' . self::TYPE_FRAGMENT . '"';
        $nodes = $this->getFileLayoutUpdatesXml()->xpath("/layouts/handle[@id=\"{$handleName}\" and ($condition)]");
        return $nodes ? reset($nodes) : null;
    }

    /**
     * Retrieve used page handle names sorted from parent to child
     *
     * @return array
     */
    public function getPageHandles()
    {
        return $this->_pageHandles;
    }

    /**
     * Retrieve full hierarchy of types and fragment types in the system
     *
     * Result format:
     * array(
     *     'handle_name_1' => array(
     *         'name'     => 'handle_name_1',
     *         'label'    => 'Handle Name 1',
     *         'children' => array(
     *             'handle_name_2' => array(
     *                 'name'     => 'handle_name_2',
     *                 'label'    => 'Handle Name 2',
     *                 'type'     => self::TYPE_PAGE or self::TYPE_FRAGMENT,
     *                 'children' => array(
     *                     // ...
     *                 )
     *             ),
     *             // ...
     *         )
     *     ),
     *     // ...
     * )
     *
     * @return array
     */
    public function getPageHandlesHierarchy()
    {
        return $this->_getPageHandleChildren('');
    }

    /**
     * Retrieve recursively all children of a page handle
     *
     * @param string $parentName
     * @return array
     */
    protected function _getPageHandleChildren($parentName)
    {
        $result = array();

        $conditions = array(
            '(@type="' . self::TYPE_PAGE . '" and ' . ($parentName ? "@parent='$parentName'" : 'not(@parent)') . ')'
        );
        if ($parentName) {
            $conditions[] = '(@type="' . self::TYPE_FRAGMENT . '" and @owner="' . $parentName . '")';
        }
        $xpath = '/layouts/*[' . implode(' or ', $conditions) . ']';
        $nodes = $this->getFileLayoutUpdatesXml()->xpath($xpath) ?: array();
        /** @var $node Magento_Core_Model_Layout_Element */
        foreach ($nodes as $node) {
            $name = $node->getAttribute('id');
            $info = array(
                'name'     => $name,
                'label'    => __((string)$node->getAttribute('label')),
                'type'     => $node->getAttribute('type'),
                'children' => array()
            );
            if ($info['type'] == self::TYPE_PAGE) {
                $info['children'] = $this->_getPageHandleChildren($name);
            }
            $result[$name] = $info;
        }
        return $result;
    }

    /**
     * Retrieve the type of a page handle
     *
     * @param string $handleName
     * @return string|null
     */
    public function getPageHandleType($handleName)
    {
        $node = $this->_getPageHandleNode($handleName);
        return $node ? $node->getAttribute('type') : null;
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @throws Magento_Exception
     * @return Magento_Core_Model_Layout_Merge
     */
    public function load($handles = array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
        } elseif (!is_array($handles)) {
            throw new Magento_Exception('Invalid layout update handle');
        }

        $this->addHandle($handles);

        $cacheId = $this->_getCacheId(md5(implode('|', $this->getHandles())));
        $result = $this->_loadCache($cacheId);
        if ($result) {
            $this->addUpdate($result);
            return $this;
        }

        foreach ($this->getHandles() as $handle) {
            $this->_merge($handle);
        }

        $this->_saveCache($this->asString(), $cacheId, $this->getHandles());
        return $this;
    }

    /**
     * Get layout updates as Magento_Core_Model_Layout_Element object
     *
     * @return SimpleXMLElement
     */
    public function asSimplexml()
    {
        $updates = trim($this->asString());
        $updates = '<' . '?xml version="1.0"?' . '><layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . $updates . '</layout>';
        return $this->_loadXmlString($updates);
    }

    /**
     * Return object representation of XML string
     *
     * @param string $xmlString
     * @return SimpleXMLElement
     */
    protected function _loadXmlString($xmlString)
    {
        return simplexml_load_string($xmlString, 'Magento_Core_Model_Layout_Element');
    }

    /**
     * Merge layout update by handle
     *
     * @param string $handle
     * @return Magento_Core_Model_Layout_Merge
     */
    protected function _merge($handle)
    {
        $this->_fetchPackageLayoutUpdates($handle);
        if ($this->_appState->isInstalled()) {
            $this->_fetchDbLayoutUpdates($handle);
        }
        return $this;
    }

    /**
     * Add updates for the specified handle
     *
     * @param string $handle
     * @return bool
     */
    protected function _fetchPackageLayoutUpdates($handle)
    {
        $_profilerKey = 'layout_package_update:' . $handle;
        Magento_Profiler::start($_profilerKey);
        $layout = $this->getFileLayoutUpdatesXml();
        foreach ($layout->xpath("handle[@id='$handle']") as $updateXml) {
            $this->_fetchRecursiveUpdates($updateXml);
            $this->addUpdate($updateXml->innerXml());
        }
        Magento_Profiler::stop($_profilerKey);

        return true;
    }

    /**
     * Fetch & add layout updates for the specified handle from the database
     *
     * @param string $handle
     * @return bool
     */
    protected function _fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout_db_update: ' . $handle;
        Magento_Profiler::start($_profilerKey);
        $updateStr = $this->_getDbUpdateString($handle);
        if (!$updateStr) {
            Magento_Profiler::stop($_profilerKey);
            return false;
        }
        $updateStr = '<update_xml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . $updateStr
            . '</update_xml>';
        $updateStr = $this->_substitutePlaceholders($updateStr);
        $updateXml = $this->_loadXmlString($updateStr);
        $this->_fetchRecursiveUpdates($updateXml);
        $this->addUpdate($updateXml->innerXml());

        Magento_Profiler::stop($_profilerKey);
        return (bool)$updateStr;
    }

    /**
     * Substitute placeholders {{placeholder_name}} with their values in XML string
     *
     * @param string $xmlString
     * @return string
     */
    protected function _substitutePlaceholders($xmlString)
    {
        if ($this->_subst === null) {
            $placeholders = array(
                'baseUrl'       => $this->_store->getBaseUrl(),
                'baseSecureUrl' => $this->_store->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_LINK, true),
            );
            $this->_subst = array();
            foreach ($placeholders as $key => $value) {
                $this->_subst['from'][] = '{{' . $key . '}}';
                $this->_subst['to'][] = $value;
            }
        }
        return str_replace($this->_subst['from'], $this->_subst['to'], $xmlString);
    }

    /**
     * Get update string
     *
     * @param string $handle
     * @return string
     */
    protected function _getDbUpdateString($handle)
    {
        return $this->_resource->fetchUpdatesByHandle($handle, $this->_theme, $this->_store);
    }

    /**
     * Add handles declared as '<update handle="handle_name"/>' directives
     *
     * @param SimpleXMLElement $updateXml
     * @return Magento_Core_Model_Layout_Merge
     */
    protected function _fetchRecursiveUpdates($updateXml)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName()) == 'update' && isset($child['handle'])) {
                $this->_merge((string)$child['handle']);
                // Adding merged layout handle to the list of applied handles
                $this->addHandle((string)$child['handle']);
            }
        }
        return $this;
    }

    /**
     * Retrieve already merged layout updates from files for specified area/theme/package/store
     *
     * @return Magento_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml()
    {
        if ($this->_layoutUpdatesCache) {
            return $this->_layoutUpdatesCache;
        }
        $cacheId = $this->_getCacheId();
        $result = $this->_loadCache($cacheId);
        if ($result) {
            $result = $this->_loadXmlString($result);
        } else {
            $result = $this->_loadFileLayoutUpdatesXml();
            $this->_saveCache($result->asXml(), $cacheId);
        }
        $this->_layoutUpdatesCache = $result;
        return $result;
    }

    /**
     * Retrieve cache identifier taking into account current area/package/theme/store
     *
     * @param string $suffix
     * @return string
     */
    protected function _getCacheId($suffix = '')
    {
        return "LAYOUT_{$this->_theme->getArea()}_STORE{$this->_store->getId()}_{$this->_theme->getId()}{$suffix}";
    }

    /**
     * Retrieve data from the cache, if the layout caching is allowed, or FALSE otherwise
     *
     * @param string $cacheId
     * @return string|bool
     */
    protected function _loadCache($cacheId)
    {
        return $this->_cache->load($cacheId);
    }

    /**
     * Save data to the cache, if the layout caching is allowed
     *
     * @param string $data
     * @param string $cacheId
     * @param array $cacheTags
     */
    protected function _saveCache($data, $cacheId, array $cacheTags = array())
    {
        $this->_cache->save($data, $cacheId, $cacheTags, null);
    }

    /**
     * Collect and merge layout updates from files
     *
     * @throws Magento_Exception
     * @return Magento_Core_Model_Layout_Element
     */
    protected function _loadFileLayoutUpdatesXml()
    {
        $layoutStr = '';
        $theme = $this->_getPhysicalTheme($this->_theme);
        $updateFiles = $this->_fileSource->getFiles($theme);
        foreach ($updateFiles as $file) {
            $fileStr = file_get_contents($file->getFilename());
            $fileStr = $this->_substitutePlaceholders($fileStr);
            /** @var $fileXml Magento_Core_Model_Layout_Element */
            $fileXml = $this->_loadXmlString($fileStr);
            if (!$file->isBase() && $fileXml->xpath(self::XPATH_HANDLE_DECLARATION)) {
                throw new Magento_Exception(sprintf(
                    "Theme layout update file '%s' must not declare page types.",
                    $file->getFileName()
                ));
            }
            $handleName = basename($file->getFilename(), '.xml');
            $handleAttributes = 'id="' . $handleName . '"' . $this->_renderXmlAttributes($fileXml);
            $handleStr = '<handle ' . $handleAttributes . '>' . $fileXml->innerXml() . '</handle>';
            $layoutStr .= $handleStr;
        }
        $layoutStr = '<layouts xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $layoutStr . '</layouts>';
        $layoutXml = $this->_loadXmlString($layoutStr);
        return $layoutXml;
    }

    /**
     * Find the closest physical theme among ancestors and a theme itself
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Theme
     * @throws Magento_Exception
     */
    protected function _getPhysicalTheme(Magento_Core_Model_Theme $theme)
    {
        $result = $theme;
        while ($result->getId() && !$result->isPhysical()) {
            $result = $result->getParentTheme();
        }
        if (!$result) {
            throw new Magento_Exception("Unable to find a physical ancestor for a theme '{$theme->getThemeTitle()}'.");
        }
        return $result;
    }

    /**
     * Return attributes of XML node rendered as a string
     *
     * @param SimpleXMLElement $node
     * @return string
     */
    protected function _renderXmlAttributes(SimpleXMLElement $node)
    {
        $result = '';
        foreach ($node->attributes() as $attributeName => $attributeValue) {
            $result .= ' ' . $attributeName . '="' . $attributeValue . '"';
        }
        return $result;
    }

    /**
     * Retrieve containers from the update handles that have been already loaded
     *
     * Result format:
     * array(
     *     'container_name' => 'Container Label',
     *     // ...
     * )
     *
     * @return array
     */
    public function getContainers()
    {
        $result = array();
        $containerNodes = $this->asSimplexml()->xpath('//container');
        /** @var $oneContainerNode Magento_Core_Model_Layout_Element */
        foreach ($containerNodes as $oneContainerNode) {
            $result[$oneContainerNode->getAttribute('name')] = __($oneContainerNode->getAttribute('label'));
        }
        return $result;
    }

    /**
     * Cleanup circular references
     *
     * Destructor should be called explicitly in order to work around the PHP bug
     * https://bugs.php.net/bug.php?id=62468
     */
    public function __destruct()
    {
        $this->_updates = array();
        $this->_layoutUpdatesCache = null;
    }
}
