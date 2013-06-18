<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Mage_GoogleOptimizer_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_googleAnalyticsHelperMock;

    /**
     * @var Mage_GoogleOptimizer_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_ConfigInterface');
        $this->_googleAnalyticsHelperMock = $this->getMock('Mage_GoogleAnalytics_Helper_Data', array(), array(), '',
            false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_helper = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Helper_Data', array(
            'storeConfig' => $this->_storeConfigMock,
            'analyticsHelper' => $this->_googleAnalyticsHelperMock,
        ));
    }

    /**
     * @param bool $isExperimentsEnabled
     * @dataProvider dataProviderBoolValues
     */
    public function testGoogleExperimentIsEnabled($isExperimentsEnabled)
    {
        $store = 1;
        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with(Mage_GoogleOptimizer_Helper_Data::XML_PATH_ENABLED, $store)
            ->will($this->returnValue($isExperimentsEnabled));

        $this->assertEquals($isExperimentsEnabled, $this->_helper->isGoogleExperimentEnabled($store));
    }

    /**
     * @return array
     */
    public function dataProviderBoolValues()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @param bool $isExperimentsEnabled
     * @param bool $isAnalyticsAvailable
     * @param bool $result
     * @dataProvider dataProviderForTestGoogleExperimentIsActive
     */
    public function testGoogleExperimentIsActive($isExperimentsEnabled, $isAnalyticsAvailable, $result)
    {
        $store = 1;
        $this->_storeConfigMock->expects($this->once())->method('getConfigFlag')
            ->with(Mage_GoogleOptimizer_Helper_Data::XML_PATH_ENABLED, $store)
            ->will($this->returnValue($isExperimentsEnabled));

        $this->_googleAnalyticsHelperMock->expects($this->any())->method('isGoogleAnalyticsAvailable')
            ->with($store)
            ->will($this->returnValue($isAnalyticsAvailable));

        $this->assertEquals($result, $this->_helper->isGoogleExperimentActive($store));
    }

    /**
     * @return array
     */
    public function dataProviderForTestGoogleExperimentIsActive()
    {
        return array(
            array(true, true, true),
            array(false, true, false),
            array(false, false, false),
            array(true, false, false),
        );
    }
}
