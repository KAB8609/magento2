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
 * Application area nodel
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_App_Area
{
    const AREA_GLOBAL   = 'global';
    const AREA_FRONTEND = 'frontend';
    const AREA_ADMIN    = 'admin';
    const AREA_ADMINHTML = 'adminhtml';

    const PART_CONFIG   = 'config';
    const PART_EVENTS   = 'events';
    const PART_TRANSLATE= 'translate';
    const PART_DESIGN   = 'design';

    /**
     * Area parameter.
     */
    const PARAM_AREA = 'area';

    /**
     * Array of area loaded parts
     *
     * @var array
     */
    protected $_loadedParts;

    /**
     * Area code
     *
     * @var string
     */
    protected $_code;

    /**
     * Event Manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Translator
     *
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Config $config
     * @param Magento_ObjectManager $objectManager
     * @param string $areaCode
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Config $config,
        Magento_ObjectManager $objectManager,
        $areaCode
    ) {
        $this->_code = $areaCode;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
        $this->_translator = $translator;
    }

    /**
     * Load area data
     *
     * @param   string|null $part
     * @return  Mage_Core_Model_App_Area
     */
    public function load($part=null)
    {
        if (is_null($part)) {
            $this->_loadPart(self::PART_CONFIG)
                ->_loadPart(self::PART_EVENTS)
                ->_loadPart(self::PART_DESIGN)
                ->_loadPart(self::PART_TRANSLATE);
        } else {
            $this->_loadPart($part);
        }
        return $this;
    }

    /**
     * Detect and apply design for the area
     *
     * @param Zend_Controller_Request_Http $request
     */
    public function detectDesign($request = null)
    {
        if ($this->_code == self::AREA_FRONTEND) {
            $designExceptionApplied = ($request && $this->_applyUserAgentDesignException($request));
            if (!$designExceptionApplied) {
                $this->_getDesignChange()
                    ->loadChange(Mage::app()->getStore()->getId())
                    ->changeDesign($this->_getDesign());
            }
        }
    }

    /**
     * Analyze user-agent information to override custom design settings
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    protected function _applyUserAgentDesignException($request)
    {
        $userAgent = $request->getServer('HTTP_USER_AGENT');
        if (empty($userAgent)) {
            return false;
        }
        try {
            $expressions = Mage::getStoreConfig('design/theme/ua_regexp');
            if (!$expressions) {
                return false;
            }
            $expressions = unserialize($expressions);
            foreach ($expressions as $rule) {
                if (preg_match($rule['regexp'], $userAgent)) {
                    $this->_getDesign()->setDesignTheme($rule['value']);
                    return true;
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * @return Mage_Core_Model_Design_Package
     */
    protected function _getDesign()
    {
        return Mage::getDesign();
    }

    /**
     * @return Mage_Core_Model_Design
     */
    protected function _getDesignChange()
    {
        return Mage::getSingleton('Mage_Core_Model_Design');
    }

    /**
     * Loading part of area
     *
     * @param   string $part
     * @return  Mage_Core_Model_App_Area
     */
    protected function _loadPart($part)
    {
        if (isset($this->_loadedParts[$part])) {
            return $this;
        }
        Magento_Profiler::start('load_area:' . $this->_code . '.' . $part,
            array('group' => 'load_area', 'area_code' => $this->_code, 'part' => $part));
        switch ($part) {
            case self::PART_CONFIG:
                $this->_initConfig();
                break;
            case self::PART_EVENTS:
                $this->_initEvents();
                break;
            case self::PART_TRANSLATE:
                $this->_initTranslate();
                break;
            case self::PART_DESIGN:
                $this->_initDesign();
                break;
        }
        $this->_loadedParts[$part] = true;
        Magento_Profiler::stop('load_area:' . $this->_code . '.' . $part);
        return $this;
    }

    /**
     * Load area configuration
     */
    protected function _initConfig()
    {
        $configurationNode = $this->_config->getNode($this->_code . '/di');
        if ($configurationNode) {
            $configuration = $configurationNode->asArray();
            $this->_objectManager->configure($configuration);
        }

    }

    /**
     * Initialize events.
     *
     * @return Mage_Core_Model_App_Area
     */
    protected function _initEvents()
    {
        $this->_eventManager->addEventArea($this->_code);
        return $this;
    }

    /**
     * Initialize translate object.
     *
     * @return Mage_Core_Model_App_Area
     */
    protected function _initTranslate()
    {
        $dispatchResult = new Varien_Object(array(
            'inline_type' => null,
            'params' => array('area' => $this->_code)
        ));
        $eventManager = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        $eventManager->dispatch('translate_initialization_before', array(
            'translate_object' => $this->_translator,
            'result' => $dispatchResult
        ));
        $this->_translator->init($this->_code, $dispatchResult, false);
        return $this;
    }

    protected function _initDesign()
    {
        if (Mage::app()->getRequest()->isStraight()) {
            return;
        }
        $this->_getDesign()->setArea($this->_code)->setDefaultDesignTheme();
    }
}
