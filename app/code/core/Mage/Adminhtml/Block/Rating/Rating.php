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
 * Ratings grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Rating_Rating extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'rating';
        $this->_headerText = Mage::helper('Mage_Rating_Helper_Data')->__('Manage Ratings');
        $this->_addButtonLabel = Mage::helper('Mage_Rating_Helper_Data')->__('Add New Rating');
        parent::__construct();
    }
}
