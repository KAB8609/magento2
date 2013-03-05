<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $installer Mage_GiftMessage_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'gift_message'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('gift_message'))
    ->addColumn('gift_message_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'GiftMessage Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer id')
    ->addColumn('sender', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Sender')
    ->addColumn('recipient', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Recipient')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Message')
    ->setComment('Gift Message');

$installer->getConnection()->createTable($table);

/**
 * Add 'gift_message_id' attributes for entities
 */
$entities = array(
    'quote',
    'quote_address',
    'quote_item',
    'quote_address_item',
    'order',
    'order_item'
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'visible'  => false,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'gift_message_id', $options);
}

/**
 * Add 'gift_message_available' attributes for entities
 */
$installer->addAttribute('order_item', 'gift_message_available', $options);
Mage::getResourceModel('Mage_Catalog_Model_Resource_Setup', array('resourceName' => 'catalog_setup'))->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, 'gift_message_available',
    array(
        'group'         => 'Gift Options',
        'backend'       => 'Mage_Catalog_Model_Product_Attribute_Backend_Boolean',
        'frontend'      => '',
        'label'         => 'Allow Gift Message',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'Mage_Eav_Model_Entity_Attribute_Source_Boolean',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => '',
        'input_renderer'   => 'Mage_GiftMessage_Block_Adminhtml_Product_Helper_Form_Config',
        'is_configurable'  => 0,
        'visible_on_front' => false
    )
);

$installer->endSetup();
