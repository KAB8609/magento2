<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_Resource_Report_Invoiced_Collection_InvoicedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Sales_Model_Resource_Report_Invoiced_Collection_Invoiced
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Report_Invoiced_Collection_Invoiced');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/invoice.php
     * @magentoDataFixture Mage/Sales/_files/report_invoiced.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array('orders_count' => 1, 'orders_invoiced' => 1),
        );
        $actualResult = array();
        /** @var Magento_Adminhtml_Model_Report_Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array(
                'orders_count' => $reportItem->getData('orders_count'),
                'orders_invoiced' => $reportItem->getData('orders_invoiced'),
            );
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
