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
 * Adminhtml search report page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize Grid Container
     *
     */
    public function __construct()
    {
        $this->_controller = 'report_search';
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Search Terms');
        parent::__construct();
        $this->_removeButton('add');
    }
}
