<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog price rules
 *
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Promo_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = Mage::helper('Mage_SalesRule_Helper_Data')->__('Shopping Cart Price Rules');
        $this->_addButtonLabel = Mage::helper('Mage_SalesRule_Helper_Data')->__('Add New Rule');
        parent::__construct();
        
    }
}
