<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Resource_Report_Refunded_Collection_RefundedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Report\Refunded\Collection\Refunded
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('\Magento\Sales\Model\Resource\Report\Refunded\Collection\Refunded');
        $this->_collection
            ->setPeriod('day')
            ->setDateRange(null, null)
            ->addStoreFilter(array(1))
        ;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/creditmemo.php
     * @magentoDataFixture Magento/Sales/_files/report_refunded.php
     */
    public function testGetItems()
    {
        $expectedResult = array(
            array(
                'orders_count' => 1,
                'refunded' => 100,
                'online_refunded' => 80,
                'offline_refunded' => 20,
            ),
        );
        $actualResult = array();
        /** @var \Magento\Adminhtml\Model\Report\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[] = array_intersect_key($reportItem->getData(), $expectedResult[0]);
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
