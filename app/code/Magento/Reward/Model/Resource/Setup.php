<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward resource setup model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * Current entity type id
     *
     * @var string
     */
    protected $_currentEntityTypeId;

    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param $resourceName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Config_Resource $resourcesConfig,
        Magento_Core_Model_Config $modulesConfig,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        Magento_Cms_Model_PageFactory $pageFactory,
        $resourceName
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($logger, $coreData, $eventManager, $resourcesConfig, $modulesConfig, $moduleList, $resource,
            $modulesReader, $cache, $migrationFactory, $resourceName);
    }

    /**
     * Add attribute to an entity type
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return Magento_Eav_Model_Entity_Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $this->_currentEntityTypeId = $entityTypeId;
        return parent::addAttribute($entityTypeId, $code, $attr);
    }

    /**
     * @return Magento_Cms_Model_Page
     */
    public function getPage()
    {
        return $this->_pageFactory->create();
    }

    /**
     * Prepare attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        if ($this->_currentEntityTypeId == 'customer') {
            $data = array_merge($data, array(
                'is_visible'                => $this->_getValue($attr, 'visible', 1),
                'is_visible_on_front'       => $this->_getValue($attr, 'visible_on_front', 0),
                'input_filter'              => $this->_getValue($attr, 'input_filter', ''),
                'lines_to_divide_multiline' => $this->_getValue($attr, 'lines_to_divide', 0),
                'min_text_length'           => $this->_getValue($attr, 'min_text_length', 0),
                'max_text_length'           => $this->_getValue($attr, 'max_text_length', 0)
            ));
        }
        return $data;
    }
}
