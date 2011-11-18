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
 * Adminhtml report review product blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Review_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'report_review_detail';

        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($this->getRequest()->getParam('id'));
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Reviews for %s', $product->getName());

        parent::__construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_review/product/'));
        $this->_addBackButton();
    }

}
