<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Setup Model of Sales Module
 */
class Magento_Sales_Model_Resource_Setup extends Magento_Eav_Model_Entity_Setup
{
    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Core_Model_Resource_Setup_MigrationFactory
     */
    protected $_migrationFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Config_Resource $resourcesConfig
     * @param Magento_Core_Model_Config $modulesConfig
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Core_Model_Resource_Resource $resourceResource
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory
     * @param Magento_Core_Model_Theme_CollectionFactory $themeFactory
     * @param Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory
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
        Magento_Core_Model_Resource_Resource $resourceResource,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Theme_CollectionFactory $themeResourceFactory,
        Magento_Core_Model_Theme_CollectionFactory $themeFactory,
        Magento_Core_Model_Resource_Setup_MigrationFactory $migrationFactory,
        $resourceName
    ) {
        parent::__construct(
            $logger, $eventManager, $resourcesConfig, $modulesConfig, $moduleList, $resource, $modulesReader, $cache,
            $resourceResource, $themeResourceFactory, $themeFactory, $migrationFactory, $resourceName
        );
        $this->_coreData = $coreData;
    }

    /**
     * List of entities converted from EAV to flat data structure
     *
     * @var $_flatEntityTables array
     */
    protected $_flatEntityTables     = array(
        'quote'             => 'sales_flat_quote',
        'quote_item'        => 'sales_flat_quote_item',
        'quote_address'     => 'sales_flat_quote_address',
        'quote_address_item'=> 'sales_flat_quote_address_item',
        'quote_address_rate'=> 'sales_flat_quote_shipping_rate',
        'quote_payment'     => 'sales_flat_quote_payment',
        'order'             => 'sales_flat_order',
        'order_payment'     => 'sales_flat_order_payment',
        'order_item'        => 'sales_flat_order_item',
        'order_address'     => 'sales_flat_order_address',
        'order_status_history' => 'sales_flat_order_status_history',
        'invoice'           => 'sales_flat_invoice',
        'invoice_item'      => 'sales_flat_invoice_item',
        'invoice_comment'   => 'sales_flat_invoice_comment',
        'creditmemo'        => 'sales_flat_creditmemo',
        'creditmemo_item'   => 'sales_flat_creditmemo_item',
        'creditmemo_comment'=> 'sales_flat_creditmemo_comment',
        'shipment'          => 'sales_flat_shipment',
        'shipment_item'     => 'sales_flat_shipment_item',
        'shipment_track'    => 'sales_flat_shipment_track',
        'shipment_comment'  => 'sales_flat_shipment_comment',
    );

    /**
     * List of entities used with separate grid table
     *
     * @var $_flatEntitiesGrid array
     */
    protected $_flatEntitiesGrid     = array(
        'order',
        'invoice',
        'shipment',
        'creditmemo'
    );

    /**
     * Check if table exist for flat entity
     *
     * @param string $table
     * @return bool
     */
    protected function _flatTableExist($table)
    {
        $tablesList = $this->getConnection()->listTables();
        return in_array(strtoupper($this->getTable($table)), array_map('strtoupper', $tablesList));
    }

    /**
     * Add entity attribute. Overwrited for flat entities support
     *
     * @param int|string $entityTypeId
     * @param string $code
     * @param array $attr
     * @return Magento_Sales_Model_Resource_Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        if (isset($this->_flatEntityTables[$entityTypeId]) &&
            $this->_flatTableExist($this->_flatEntityTables[$entityTypeId]))
        {
            $this->_addFlatAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr);
            $this->_addGridAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr, $entityTypeId);
        } else {
            parent::addAttribute($entityTypeId, $code, $attr);
        }
        return $this;
    }

    /**
     * Add attribute as separate column in the table
     *
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @return Magento_Sales_Model_Resource_Setup
     */
    protected function _addFlatAttribute($table, $attribute, $attr)
    {
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (isset($tableInfo[$attribute])) {
            return $this;
        }
        $columnDefinition = $this->_getAttributeColumnDefinition($attribute, $attr);
        $this->getConnection()->addColumn($this->getTable($table), $attribute, $columnDefinition);
        return $this;
    }

    /**
     * Add attribute to grid table if necessary
     *
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @param string $entityTypeId
     * @return Magento_Sales_Model_Resource_Setup
     */
    protected function _addGridAttribute($table, $attribute, $attr, $entityTypeId)
    {
        if (in_array($entityTypeId, $this->_flatEntitiesGrid) && !empty($attr['grid'])) {
            $columnDefinition = $this->_getAttributeColumnDefinition($attribute, $attr);
            $this->getConnection()->addColumn($this->getTable($table . '_grid'), $attribute, $columnDefinition);
        }
        return $this;
    }

    /**
     * Retrieve definition of column for create in flat table
     *
     * @param string $code
     * @param array $data
     * @return array
     */
    protected function _getAttributeColumnDefinition($code, $data)
    {
        // Convert attribute type to column info
        $data['type'] = isset($data['type']) ? $data['type'] : 'varchar';
        $type = null;
        $length = null;
        switch ($data['type']) {
            case 'timestamp':
                $type = Magento_DB_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'datetime':
                $type = Magento_DB_Ddl_Table::TYPE_DATETIME;
                break;
            case 'decimal':
                $type = Magento_DB_Ddl_Table::TYPE_DECIMAL;
                $length = '12,4';
                break;
            case 'int':
                $type = Magento_DB_Ddl_Table::TYPE_INTEGER;
                break;
            case 'text':
                $type = Magento_DB_Ddl_Table::TYPE_TEXT;
                $length = 65536;
                break;
            case 'char':
            case 'varchar':
                $type = Magento_DB_Ddl_Table::TYPE_TEXT;
                $length = 255;
                break;
        }
        if ($type !== null) {
            $data['type'] = $type;
            $data['length'] = $length;
        }

        $data['nullable'] = isset($data['required']) ? !$data['required'] : true;
        $data['comment']  = isset($data['comment']) ? $data['comment'] : ucwords(str_replace('_', ' ', $code));
        return $data;
    }

    public function getDefaultEntities()
    {
        $entities = array(
            'order'                       => array(
                'entity_model'                   => 'Magento_Sales_Model_Resource_Order',
                'table'                          => 'sales_flat_order',
                'increment_model'                => 'Magento_Eav_Model_Entity_Increment_Numeric',
                'increment_per_store'            => true,
                'attributes'                     => array()
            ),
            'invoice'                       => array(
                'entity_model'                   => 'Magento_Sales_Model_Resource_Order_Invoice',
                'table'                          => 'sales_flat_invoice',
                'increment_model'                => 'Magento_Eav_Model_Entity_Increment_Numeric',
                'increment_per_store'            => true,
                'attributes'                     => array()
            ),
            'creditmemo'                       => array(
                'entity_model'                   => 'Magento_Sales_Model_Resource_Order_Creditmemo',
                'table'                          => 'sales_flat_creditmemo',
                'increment_model'                => 'Magento_Eav_Model_Entity_Increment_Numeric',
                'increment_per_store'            => true,
                'attributes'                     => array()
            ),
            'shipment'                       => array(
                'entity_model'                   => 'Magento_Sales_Model_Resource_Order_Shipment',
                'table'                          => 'sales_flat_shipment',
                'increment_model'                => 'Magento_Eav_Model_Entity_Increment_Numeric',
                'increment_per_store'            => true,
                'attributes'                     => array()
            )
        );
        return $entities;
    }

    /**
     * Get Core Helper
     *
     * @return Magento_Core_Helper_Data
     */
    public function getCoreData()
    {
        return $this->_coreData;
    }

    /**
     * Get config model
     *
     * @return Magento_Core_Model_Config
     */
    public function getConfigModel()
    {
        return $this->_config;
    }
}
