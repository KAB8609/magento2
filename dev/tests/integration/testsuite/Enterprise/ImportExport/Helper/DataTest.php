<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_ImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_ImportExport_Helper_Data
     */
    protected $_importExportHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleManagerMock;

    /**
     * Set import/export helper
     *
     * @static
     */
    protected function setUp()
    {
        $this->_moduleManagerMock = $this->getMock('Mage_Core_Model_ModuleManager', array(), array(), '', false);
        $context = Mage::getModel('Mage_Core_Helper_Context', array('moduleManager' => $this->_moduleManagerMock));
        $this->_importExportHelper = Mage::getObjectManager()->create(
            'Enterprise_ImportExport_Helper_Data', array('context' => $context)
        );
    }

    /**
     * Is reward points enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled 1
     */
    public function testIsRewardPointsEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_Reward')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled 0
     */
    public function testIsRewardPointsEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_Reward')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled 1
     */
    public function testIsRewardPointsEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_Reward')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled 0
     */
    public function testIsRewardPointsEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_Reward')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is customer balance enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled 1
     */
    public function testisCustomerBalanceEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_CustomerBalance')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled 0
     */
    public function testisCustomerBalanceEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_CustomerBalance')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled 1
     */
    public function testisCustomerBalanceEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_CustomerBalance')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled 0
     */
    public function testisCustomerBalanceEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Enterprise_CustomerBalance')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }
}
