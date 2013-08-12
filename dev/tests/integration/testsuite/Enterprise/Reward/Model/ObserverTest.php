<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Reward_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/ImportExport/_files/customer.php
     * @dataProvider saveRewardPointsDataProvider
     *
     * @param integer $pointsDelta
     * @param integer $expectedBalance
     */
    public function testSaveRewardPoints($pointsDelta, $expectedBalance)
    {
        $customer = Mage::registry('_fixture/Mage_ImportExport_Customer');

        $this->_saveRewardPoints($customer, $pointsDelta);

        /** @var $reward Enterprise_Reward_Model_Reward */
        $reward = Mage::getModel('Enterprise_Reward_Model_Reward');
        $reward->setCustomer($customer)
            ->loadByCustomer();

        $this->assertEquals($expectedBalance, $reward->getPointsBalance());
    }

    public function saveRewardPointsDataProvider()
    {
        return array(
            'points delta is not set' => array(
                '$pointsDelta' => '',
                '$expectedBalance' => null
            ),
            'points delta is positive' => array(
                '$pointsDelta' => 100,
                '$expectedBalance' => 100
            )
        );
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @param mixed $pointsDelta
     */
    protected function _saveRewardPoints(Mage_Customer_Model_Customer $customer, $pointsDelta = '')
    {
        $reward = array(
            'points_delta' => $pointsDelta
        );

        $request = new Magento_Test_Request();
        $request->setPost(
            array('reward' => $reward)
        );

        $event = new Magento_Event(
            array(
                'request'  => $request,
                'customer' => $customer
            )
        );

        $eventObserver = new Magento_Event_Observer(
            array('event' => $event)
        );

        $rewardObserver = Mage::getModel('Enterprise_Reward_Model_Observer');
        $rewardObserver->saveRewardPoints($eventObserver);
    }
}
