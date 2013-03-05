<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme model class
 *
 * @method Mage_Core_Model_Theme save()
 * @method string getPackageCode()
 * @method string getThemePath()
 * @method string getParentThemePath()
 * @method string getParentId()
 * @method string getArea()
 * @method string getThemeTitle()
 * @method string getThemeVersion()
 * @method string getPreviewImage()
 * @method string getMagentoVersionFrom()
 * @method string getMagentoVersionTo()
 * @method bool getIsFeatured()
 * @method int getThemeId()
 * @method int getType()
 * @method array getAssignedStores()
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Core_Model_Theme setAssignedStores(array $stores)
 * @method Mage_Core_Model_Theme addData(array $data)
 * @method Mage_Core_Model_Theme setParentId(int $id)
 * @method Mage_Core_Model_Theme setParentTheme($parentTheme)
 * @method Mage_Core_Model_Theme setPackageCode(string $packageCode)
 * @method Mage_Core_Model_Theme setThemeCode(string $themeCode)
 * @method Mage_Core_Model_Theme setThemePath(string $themePath)
 * @method Mage_Core_Model_Theme setThemeVersion(string $themeVersion)
 * @method Mage_Core_Model_Theme setArea(string $area)
 * @method Mage_Core_Model_Theme setThemeTitle(string $themeTitle)
 * @method Mage_Core_Model_Theme setMagentoVersionFrom(string $versionFrom)
 * @method Mage_Core_Model_Theme setMagentoVersionTo(string $versionTo)
 * @method Mage_Core_Model_Theme setType(string $type)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
    implements Mage_Core_Model_Theme_Customization_CustomizedInterface
{
    /**#@+
     * Theme types group
     */
    const TYPE_PHYSICAL = 0;
    const TYPE_VIRTUAL = 1;
    const TYPE_STAGING = 2;
    /**#@-*/

    /**
     * Cache tag for empty theme
     */
    const CACHE_TAG_NO_THEME = 'NO_THEME';

    /**
     * Separator between theme_path elements
     */
    const PATH_SEPARATOR = '/';

    /**
     * Path prefix to customized theme files
     */
    const PATH_PREFIX_CUSTOMIZATION = 'customization';

    /**
     * Labels collection array
     *
     * @var array
     */
    protected $_labelsCollection;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Array of theme customizations for save
     *
     * @var array
     */
    protected $_themeCustomizations = array();

    /**
     * @var Mage_Core_Model_Theme_Image
     */
    protected $_themeImage;

    /**
     * @var Mage_Core_Model_Theme_Domain_Factory
     */
    protected $_domainFactory;

    /**
     * All possible types of a theme
     *
     * @var array
     */
    public static $types = array(
        self::TYPE_PHYSICAL,
        self::TYPE_VIRTUAL,
        self::TYPE_STAGING,
    );

    /**
     * @param Mage_Core_Model_Context $context
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_Image $themeImage
     * @param Mage_Core_Model_Resource_Theme $resource
     * @param Mage_Core_Model_Theme_Domain_Factory $domainFactory
     * @param Mage_Core_Model_Resource_Theme_Collection $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Theme_Image $themeImage,
        Mage_Core_Model_Resource_Theme $resource,
        Mage_Core_Model_Theme_Domain_Factory $domainFactory,
        Mage_Core_Model_Resource_Theme_Collection $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_objectManager = $objectManager;
        $this->_themeFactory = $themeFactory;
        $this->_helper = $helper;
        $this->_domainFactory = $domainFactory;
        $this->_themeImage = $themeImage->setTheme($this);
    }

    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Get theme image model
     *
     * @return Mage_Core_Model_Theme_Image
     */
    public function getThemeImage()
    {
        return $this->_themeImage;
    }

    /**
     * Themes collection loaded from file system configurations
     *
     * @return Mage_Core_Model_Theme_Collection
     */
    public function getCollectionFromFilesystem()
    {
        return $this->_objectManager->get('Mage_Core_Model_Theme_Collection');
    }

    /**
     * Validate theme data
     *
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _validate()
    {
        /** @var $validator Mage_Core_Model_Theme_Validator */
        $validator = $this->_objectManager->get('Mage_Core_Model_Theme_Validator');
        if (!$validator->validate($this)) {
            $messages = $validator->getErrorMessages();
            throw new Mage_Core_Exception(implode(PHP_EOL, reset($messages)));
        }
        return $this;
    }

    /**
     * Check if theme is deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        return $this->isVirtual();
    }

    /**
     * Check if theme is editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->isVirtual();
    }

    /**
     * Check theme is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return self::TYPE_VIRTUAL == $this->getType();
    }

    /**
     * Check theme is visible in backend
     *
     * @return bool
     */
    public function isVisible()
    {
        return in_array($this->getType(), array(self::TYPE_PHYSICAL, self::TYPE_VIRTUAL));
    }

    /**
     * Check theme is existing in filesystem
     *
     * @return bool
     */
    public function isPresentInFilesystem()
    {
        $collection = $this->getCollectionFromFilesystem()->addDefaultPattern('*')->getItems();
        return $this->getThemePath() && isset($collection[$this->getFullPath()]);
    }

    /**
     * Check is theme has child themes
     *
     * @return bool
     */
    public function hasChildThemes()
    {
        return (bool)$this->getCollection()->addFieldToFilter('parent_id', array('eq' => $this->getId()))->getSize();
    }

    /**
     * Return path to customized theme files
     *
     * @return string|null
     */
    public function getCustomizationPath()
    {
        $customPath = $this->getData('customization_path');
        if ($this->getId() && empty($customPath)) {
            /** @var $modelDir Mage_Core_Model_Dir */
            $modelDir = $this->_objectManager->get('Mage_Core_Model_Dir');
            $customPath = $modelDir->getDir(Mage_Core_Model_Dir::THEME) . DIRECTORY_SEPARATOR
                . self::PATH_PREFIX_CUSTOMIZATION . DIRECTORY_SEPARATOR . $this->getId();
            $this->setData('customization_path', $customPath);
        }
        return $customPath;
    }

    /**
     * Return theme customization collection by type
     *
     * @param string $type
     * @return Varien_Data_Collection
     * @throws InvalidArgumentException
     */
    public function getCustomizationData($type)
    {
        if (!isset($this->_themeCustomizations[$type])) {
            throw new InvalidArgumentException('Customization is not present');
        }
        return $this->_themeCustomizations[$type]->getCollectionByTheme($this);
    }

    /**
     * Add theme customization
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizationInterface $customization
     * @return Mage_Core_Model_Theme
     */
    public function setCustomization(Mage_Core_Model_Theme_Customization_CustomizationInterface $customization)
    {
        $this->_themeCustomizations[$customization->getType()] = $customization;
        return $this;
    }

    /**
     * Save all theme customization object
     *
     * @return Mage_Core_Model_Theme
     */
    public function saveThemeCustomization()
    {
        /** @var $file Mage_Core_Model_Theme_Customization_CustomizationInterface */
        foreach ($this->_themeCustomizations as $file) {
            $file->saveData($this);
        }
        return $this;
    }

    /**
     * Include customized files on default handle
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _applyCustomizationFiles()
    {
        if (!$this->isCustomized()) {
            return $this;
        }
        /** @var $link Mage_Core_Model_Theme_Customization_Link */
        $link = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Link');
        $link->setThemeId($this->getId())->changeCustomFilesUpdate();
        return $this;
    }

    /**
     * Check if theme object data was changed.
     *
     * @return bool
     */
    public function hasDataChanges()
    {
        return parent::hasDataChanges() || $this->isCustomized();
    }

    /**
     * Check whether present customization objects
     *
     * @return bool
     */
    public function isCustomized()
    {
        return !empty($this->_themeCustomizations);
    }

    /**
     * Update all relations after deleting theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _afterSave()
    {
        $this->saveThemeCustomization();
        if ($this->isCustomized()) {
            $this->_applyCustomizationFiles();
        }

        $this->_checkAssignedThemeChanged();
        return parent::_afterSave();
    }

    /**
     * Before theme save
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _beforeSave()
    {
        $this->_validate();
        return parent::_beforeSave();
    }

    /**
     * Processing theme before deleting data
     *
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        if (!$this->isDeletable()) {
            throw new Mage_Core_Exception($this->_helper->__('Theme isn\'t deletable.'));
        }
        $this->getThemeImage()->removePreviewImage();
        return parent::_beforeDelete();
    }

    /**
     * Check is theme assigned to store and dispatch event if that was changed
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _checkAssignedThemeChanged()
    {
        /** @var $service Mage_Core_Model_Theme_Service */
        $service = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
        if ($service->isThemeAssignedToStore($this)) {
            $this->_eventDispatcher->dispatch('assigned_theme_changed', array('theme' => $this));
        }
        return $this;
    }

    /**
     * Update all relations after deleting theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _afterDelete()
    {
        $this->getCollection()->updateChildRelations($this);
        return parent::_afterDelete();
    }

    /**
     * Get parent theme model
     *
     * @return Mage_Core_Model_Theme|null
     */
    public function getParentTheme()
    {
        if ($this->hasData('parent_theme')) {
            return $this->getData('parent_theme');
        }

        $theme = null;
        if ($this->getParentId()) {
            $theme = $this->_themeFactory->create()->load($this->getParentId());
        }
        $this->setParentTheme($theme);
        return $theme;
    }

    /**
     * Save data from form
     *
     * @param array $themeData
     * @return Mage_Core_Model_Theme
     */
    public function saveFormData($themeData)
    {
        if (isset($themeData['theme_id'])) {
            $this->load($themeData['theme_id']);
            if ($this->getId() && !$this->isEditable()) {
                Mage::throwException($this->_helper->__('Theme isn\'t editable.'));
            }
        }
        $previewImageData = array();
        if (isset($themeData['preview_image'])) {
            $previewImageData = $themeData['preview_image'];
            unset($themeData['preview_image']);
        }
        $this->addData($themeData);

        if (isset($previewImageData['delete'])) {
            $this->getThemeImage()->removePreviewImage();
        }

        $this->getThemeImage()->uploadPreviewImage('preview_image');
        $this->setType(self::TYPE_VIRTUAL)->setArea(Mage_Core_Model_App_Area::AREA_FRONTEND)->save();
        return $this;
    }

    /**
     * Return cache key for current theme
     *
     * @return string
     */
    public function getCacheKey()
    {
        if (!$this->getId()) {
            return self::CACHE_TAG_NO_THEME . $this->getThemePath();
        }

        return $this->getId() . $this->getThemePath();
    }

    /**
     * Retrieve theme full path which is used to distinguish themes if they are not in DB yet
     *
     * Alternative id looks like "<area>/<package_code>/<theme_code>".
     * Used as id in file-system theme collection
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->getArea() . self::PATH_SEPARATOR . $this->getThemePath();
    }

    /**
     * Check if the theme is compatible with Magento version
     *
     * @return bool
     */
    public function isThemeCompatible()
    {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, $this->getMagentoVersionFrom(), '>=')) {
            if ($this->getMagentoVersionTo() == '*'
                || version_compare($magentoVersion, $this->getMagentoVersionFrom(), '<=')
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the theme is compatible with Magento version and mark theme label if not compatible
     *
     * @return Mage_Core_Model_Theme
     */
    public function checkThemeCompatible()
    {
        if (!$this->isThemeCompatible()) {
            $this->setThemeTitle($this->_helper->__('%s (incompatible version)', $this->getThemeTitle()));
        }
        return $this;
    }

    /**
     * Return labels collection array
     *
     * @param bool|string $label add empty values to result with specific label
     * @return array
     */
    public function getLabelsCollection($label = false)
    {
        if (!$this->_labelsCollection) {
            /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
            $themeCollection = $this->getCollection();
            $themeCollection->setOrder('theme_title', Varien_Data_Collection::SORT_ORDER_ASC)
                ->filterVisibleThemes()
                ->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
                ->walk('checkThemeCompatible');
            $this->_labelsCollection = $themeCollection->toOptionArray();
        }
        $options = $this->_labelsCollection;
        if ($label) {
            array_unshift($options, array('value' => '', 'label' => $label));
        }
        return $options;
    }

    /**
     * Return labels collection for backend system configuration with empty value "No Theme"
     *
     * @return array
     */
    public function getLabelsCollectionForSystemConfiguration()
    {
        return $this->getLabelsCollection($this->_helper->__('-- No Theme --'));
    }

    /**
     * Clear data for clone
     */
    public function __clone()
    {
        $this->unsetData()->setOrigData();
    }

    /**
     * Get one of theme domain models
     *
     * @param int|null $type
     * @return Mage_Core_Model_Theme_Domain_Physical|Mage_Core_Model_Theme_Domain_Virtual|Mage_Core_Model_Theme_Domain_Staging
     * @throws Mage_Core_Exception
     */
    public function getDomainModel($type = null)
    {
        if ($type !== null) {
            if ($type != $this->getType()) {
                throw new Mage_Core_Exception(
                    sprintf('Invalid domain model "%s" requested for theme "%s" of type "%s"',
                        $type, $this->getId(), $this->getType()
                    )
                );
            }
        }

        return $this->_domainFactory->create($this);
    }
}
