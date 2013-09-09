<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance Model
 *
 * @method Magento_Widget_Model_Resource_Widget_Instance _getResource()
 * @method Magento_Widget_Model_Resource_Widget_Instance getResource()
 * @method string getTitle()
 * @method Magento_Widget_Model_Widget_Instance setTitle(string $value)
 * @method Magento_Widget_Model_Widget_Instance setStoreIds(string $value)
 * @method Magento_Widget_Model_Widget_Instance setWidgetParameters(string $value)
 * @method int getSortOrder()
 * @method Magento_Widget_Model_Widget_Instance setSortOrder(int $value)
 * @method Magento_Widget_Model_Widget_Instance setThemeId(int $value)
 * @method int getThemeId()
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Model_Widget_Instance extends Magento_Core_Model_Abstract
{
    const SPECIFIC_ENTITIES = 'specific';
    const ALL_ENTITIES      = 'all';

    const DEFAULT_LAYOUT_HANDLE            = 'default';
    const PRODUCT_LAYOUT_HANDLE            = 'catalog_product_view';
    const SINGLE_PRODUCT_LAYOUT_HANLDE     = 'catalog_product_view_id_{{ID}}';
    const PRODUCT_TYPE_LAYOUT_HANDLE       = 'catalog_product_view_type_{{TYPE}}';
    const ANCHOR_CATEGORY_LAYOUT_HANDLE    = 'catalog_category_view_type_layered';
    const NOTANCHOR_CATEGORY_LAYOUT_HANDLE = 'catalog_category_view_type_default';
    const SINGLE_CATEGORY_LAYOUT_HANDLE    = 'catalog_category_view_{{ID}}';

    const XML_NODE_RELATED_CACHE = 'global/widget/related_cache_types';

    /** @var array  */
    protected $_layoutHandles = array();

    /** @var array */
    protected $_specificEntitiesLayoutHandles = array();

    /**
     * @var Magento_Simplexml_Element
     */
    protected $_widgetConfigXml = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'widget_widget_instance';

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /** @var  Magento_Widget_Model_Widget */
    protected $_widgetModel;

    /** @var  Magento_Core_Model_Config */
    protected $_coreConfig;

    /**
     * @var Magento_Widget_Model_Config_Reader
     */
    protected $_reader;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     * @param Magento_Widget_Model_Config_Reader $reader,
     * @param Magento_Widget_Model_Widget $widgetModel,
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_View_FileSystem $viewFileSystem,
        Magento_Widget_Model_Config_Reader $reader,
        Magento_Widget_Model_Widget $widgetModel,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_viewFileSystem = $viewFileSystem;
        $this->_reader = $reader;
        $this->_widgetModel = $widgetModel;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Widget_Model_Resource_Widget_Instance');
        $this->_layoutHandles = array(
            'anchor_categories' => self::ANCHOR_CATEGORY_LAYOUT_HANDLE,
            'notanchor_categories' => self::NOTANCHOR_CATEGORY_LAYOUT_HANDLE,
            'all_products' => self::PRODUCT_LAYOUT_HANDLE,
            'all_pages' => self::DEFAULT_LAYOUT_HANDLE
        );
        $this->_specificEntitiesLayoutHandles = array(
            'anchor_categories' => self::SINGLE_CATEGORY_LAYOUT_HANDLE,
            'notanchor_categories' => self::SINGLE_CATEGORY_LAYOUT_HANDLE,
            'all_products' => self::SINGLE_PRODUCT_LAYOUT_HANLDE,
        );
        foreach (array_keys(Magento_Catalog_Model_Product_Type::getTypes()) as $typeId) {
            $layoutHandle = str_replace('{{TYPE}}', $typeId, self::PRODUCT_TYPE_LAYOUT_HANDLE);
            $this->_layoutHandles[$typeId . '_products'] = $layoutHandle;
            $this->_specificEntitiesLayoutHandles[$typeId . '_products'] = self::SINGLE_PRODUCT_LAYOUT_HANLDE;
        }
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Widget_Model_Widget_Instance
     */
    protected function _beforeSave()
    {
        $pageGroupIds = array();
        $tmpPageGroups = array();
        $pageGroups = $this->getData('page_groups');
        if ($pageGroups) {
            foreach ($pageGroups as $pageGroup) {
                if (isset($pageGroup[$pageGroup['page_group']])) {
                    $pageGroupData = $pageGroup[$pageGroup['page_group']];
                    if ($pageGroupData['page_id']) {
                        $pageGroupIds[] = $pageGroupData['page_id'];
                    }
                    if ($pageGroup['page_group'] == 'pages') {
                        $layoutHandle = $pageGroupData['layout_handle'];
                    } else {
                        $layoutHandle = $this->_layoutHandles[$pageGroup['page_group']];
                    }
                    if (!isset($pageGroupData['template'])) {
                        $pageGroupData['template'] = '';
                    }
                    $tmpPageGroup = array(
                        'page_id' => $pageGroupData['page_id'],
                        'group' => $pageGroup['page_group'],
                        'layout_handle' => $layoutHandle,
                        'for' => $pageGroupData['for'],
                        'block_reference' => $pageGroupData['block'],
                        'entities' => '',
                        'layout_handle_updates' => array($layoutHandle),
                        'template' => $pageGroupData['template']?$pageGroupData['template']:''
                    );
                    if ($pageGroupData['for'] == self::SPECIFIC_ENTITIES) {
                        $layoutHandleUpdates = array();
                        foreach (explode(',', $pageGroupData['entities']) as $entity) {
                            $layoutHandleUpdates[] = str_replace('{{ID}}', $entity,
                                $this->_specificEntitiesLayoutHandles[$pageGroup['page_group']]);
                        }
                        $tmpPageGroup['entities'] = $pageGroupData['entities'];
                        $tmpPageGroup['layout_handle_updates'] = $layoutHandleUpdates;
                    }
                    $tmpPageGroups[] = $tmpPageGroup;
                }
            }
        }
        if (is_array($this->getData('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getData('store_ids')));
        }
        if (is_array($this->getData('widget_parameters'))) {
            $this->setData('widget_parameters', serialize($this->getData('widget_parameters')));
        }
        $this->setData('page_groups', $tmpPageGroups);
        $this->setData('page_group_ids', $pageGroupIds);

        return parent::_beforeSave();
    }

    /**
     * Validate widget instance data
     *
     * @return string|boolean
     */
    public function validate()
    {
        if ($this->isCompleteToCreate()) {
            return true;
        }
        return __('We cannot create the widget instance because it is missing required information.');
    }

    /**
     * Check if widget instance has required data (other data depends on it)
     *
     * @return boolean
     */
    public function isCompleteToCreate()
    {
        return $this->getType() && $this->getThemeId();
    }

    /**
     * Setter
     * Prepare widget type
     *
     * @param string $type
     * @return Magento_Widget_Model_Widget_Instance
     */
    public function setType($type)
    {
        $this->setData('instance_type', $type);
        return $this;
    }

    /**
     * Getter
     * Prepare widget type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData('instance_type');
    }

    /**
     * Getter.
     * If not set return default
     *
     * @return string
     */
    public function getArea()
    {
        //TODO Shouldn't we get "area" from theme model which we can load using "theme_id"?
        if (!$this->_getData('area')) {
            return Magento_Core_Model_View_DesignInterface::DEFAULT_AREA;
        }
        return $this->_getData('area');
    }

    /**
     * Getter
     * Explode to array if string setted
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (is_string($this->getData('store_ids'))) {
            return explode(',', $this->getData('store_ids'));
        }
        return $this->getData('store_ids');
    }

    /**
     * Getter
     * Unserialize if serialized string setted
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        if (is_string($this->getData('widget_parameters'))) {
            return unserialize($this->getData('widget_parameters'));
        } else if (is_null($this->getData('widget_parameters'))) {
            return array();
        }
        return (is_array($this->getData('widget_parameters'))) ? $this->getData('widget_parameters') : array();
    }

    /**
     * Retrieve option array of widget types
     *
     * @return array
     */
    public function getWidgetsOptionArray()
    {
        $widgets = array();
        $widgetsArr = $this->_widgetModel->getWidgetsArray();
        foreach ($widgetsArr as $widget) {
            $widgets[] = array(
                'value' => $widget['type'],
                'label' => $widget['name']
            );
        }
        return $widgets;
    }

    /**
     * Load widget XML config and merge with theme widget config
     *
     * @return array|null
     */
    public function getWidgetConfigAsArray()
    {
        if ($this->_widgetConfigXml === null) {
            $this->_widgetConfigXml = $this->_widgetModel->getWidgetByClassType($this->getType());
            if ($this->_widgetConfigXml) {
                $configFile = $this->_viewFileSystem->getFilename('widget.xml', array(
                    'area'   => $this->getArea(),
                    'theme'  => $this->getThemeId(),
                    'module' => $this->_coreConfig->determineOmittedNamespace(
                        preg_replace('/^(.+?)\/.+$/', '\\1', $this->getType()), true
                    ),
                ));

                if (is_readable($configFile)) {
                    $config = $this->_reader->readFile($configFile);
                    $widgetName = isset($this->_widgetConfigXml['name']) ? $this->_widgetConfigXml['name'] : null;
                    $themeWidgetConfig = null;
                    if (!is_null($widgetName)) {
                        foreach ($config as $widget) {
                            if (isset($widget['name']) && ($widgetName === $widget['name'])) {
                                $themeWidgetConfig = $widget;
                                break;
                            }
                        }
                    }
                    if ($themeWidgetConfig) {
                        $this->_widgetConfigXml = array_replace_recursive($this->_widgetConfigXml, $themeWidgetConfig);
                    }
                }
            }
        }
        return $this->_widgetConfigXml;
    }

    /**
     * Retrieve widget available templates
     *
     * @return array
     */
    public function getWidgetTemplates()
    {
        $templates = array();
        $widgetConfig = $this->getWidgetConfigAsArray();
        if ($widgetConfig && isset($widgetConfig['parameters'])
            && isset($widgetConfig['parameters']['template'])) {
            $configTemplates = $widgetConfig['parameters']['template'];
            if (isset($configTemplates['values'])) {
                foreach ($configTemplates['values'] as $name => $template) {
                    $templates[(string)$name] = array(
                        'value' => (string)$template['value'],
                        'label' => __((string)$template['label'])->render()
                    );
                }
            }
        }
        return $templates;
    }

    /**
     * Get list of containers that widget is limited to be in
     *
     * @return array
     */
    public function getWidgetSupportedContainers()
    {
        $containers = array();
        $widgetConfig = $this->getWidgetConfigAsArray();
        if (isset($widgetConfig) && isset($widgetConfig['supported_containers'])) {
            $configNodes = $widgetConfig['supported_containers'];
            foreach ($configNodes as $node) {
                if (isset($node['container_name'])) {
                    $containers[] = (string)$node['container_name'];
                }
            }
        }
        return $containers;
    }

    /**
     * Retrieve widget templates that supported by specified container name
     *
     * @param string $containerName
     * @return array
     */
    public function getWidgetSupportedTemplatesByContainer($containerName)
    {
        $templates = array();
        $widgetTemplates = $this->getWidgetTemplates();
        $widgetConfig = $this->getWidgetConfigAsArray();
        if (isset($widgetConfig)) {
            if (!isset($widgetConfig['supported_containers'])) {
                return $widgetTemplates;
            }
            $configNodes = $widgetConfig['supported_containers'];
            foreach ($configNodes as $node) {
                if (isset($node['container_name']) && ((string)$node['container_name'] == $containerName)) {
                    if (isset($node['template'])) {
                        $templateChildren = $node['template'];
                        foreach ($templateChildren as $template) {
                            if (isset($widgetTemplates[(string)$template])) {
                                $templates[] = $widgetTemplates[(string)$template];
                            }
                        }
                    }
                }
            }
        } else {
            return $widgetTemplates;
        }
        return $templates;
    }

    /**
     * Generate layout update xml
     *
     * @param string $container
     * @param string $templatePath
     * @return string
     */
    public function generateLayoutUpdateXml($container, $templatePath = '')
    {
        $templateFilename = $this->_viewFileSystem->getFilename($templatePath, array(
            'area'    => $this->getArea(),
            'themeId' => $this->getThemeId(),
            'module'  => Magento_Core_Block_Abstract::extractModuleName($this->getType()),
        ));
        if (!$this->getId() && !$this->isCompleteToCreate() || ($templatePath && !is_readable($templateFilename))) {
            return '';
        }
        $parameters = $this->getWidgetParameters();
        $xml = '<reference name="' . $container . '">';
        $template = '';
        if (isset($parameters['template'])) {
            unset($parameters['template']);
        }
        if ($templatePath) {
            $template = ' template="' . $templatePath . '"';
        }

        $hash = Mage::helper('Magento_Core_Helper_Data')->uniqHash();
        $xml .= '<block class="' . $this->getType() . '" name="' . $hash . '"' . $template . '>';
        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            if ($name && strlen((string)$value)) {
                $xml .= '<action method="setData">'
                    . '<argument name="name" xsi:type="string">' . $name . '</argument>'
                    . '<argument name="value" xsi:type="string">'
                    . Mage::helper('Magento_Widget_Helper_Data')->escapeHtml($value) . '</argument>'
                    . '</action>';
            }
        }
        $xml .= '</block></reference>';

        return $xml;
    }

    /**
     * Invalidate related cache types
     *
     * @return Magento_Widget_Model_Widget_Instance
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            /** @var Magento_Core_Model_Cache_TypeListInterface $cacheTypeList */
            $cacheTypeList = Mage::getObjectManager()->get('Magento_Core_Model_Cache_TypeListInterface');
            $cacheTypeList->invalidate($types);
        }
        return $this;
    }

    /**
     * Invalidate related cache if instance contain layout updates
     */
    protected function _afterSave()
    {
        if ($this->dataHasChangedFor('page_groups') || $this->dataHasChangedFor('widget_parameters')) {
            $this->_invalidateCache();
        }
        return parent::_afterSave();
    }

    /**
     * Invalidate related cache if instance contain layout updates
     */
    protected function _beforeDelete()
    {
        if ($this->getPageGroups()) {
            $this->_invalidateCache();
        }
        return parent::_beforeDelete();
    }
}
