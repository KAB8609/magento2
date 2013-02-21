<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Report_Sales_Bestsellers_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Report_Sales_Bestsellers_Grid
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Report_Sales_Bestsellers_Grid');
    }

    public function testGetResourceCollectionName()
    {
        $collectionName = $this->_block->getResourceCollectionName();
        $this->assertTrue(class_exists($collectionName));
    }
}
