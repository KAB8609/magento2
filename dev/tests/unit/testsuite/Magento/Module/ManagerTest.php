<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Module;

class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * XPath in the configuration of a module output flag
     */
    const XML_PATH_OUTPUT_ENABLED = 'custom/is_module_output_enabled';

    /**
     * @var \Magento\Module\Manager
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_outputConfig;

    protected function setUp()
    {
        $this->_moduleList = $this->getMockForAbstractClass('Magento\Module\ModuleListInterface');
        $this->_outputConfig = $this->getMockForAbstractClass('Magento\Module\Output\ConfigInterface');
        $this->_model = new \Magento\Module\Manager($this->_outputConfig, $this->_moduleList, array(
            'Module_DisabledOutputOne' => self::XML_PATH_OUTPUT_ENABLED,
            'Module_DisabledOutputTwo' => 'Magento\Module\ManagerTest::XML_PATH_OUTPUT_ENABLED',
        ));
    }

    public function testIsEnabledReturnsTrueForActiveModule()
    {
        $this->_moduleList->expects($this->once())->method('getModule')
            ->will($this->returnValue(array('name' => 'Some_Module')));
        $this->assertTrue($this->_model->isEnabled('Some_Module'));
    }

    public function testIsEnabledReturnsFalseForInactiveModule()
    {
        $this->_moduleList->expects($this->once())->method('getModule');
        $this->assertFalse($this->_model->isEnabled('Some_Module'));
    }

    public function testIsOutputEnabledReturnsFalseForDisabledModule()
    {
        $this->_outputConfig
            ->expects($this->any())
            ->method('getFlag')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_model->isOutputEnabled('Nonexisting_Module'));
    }

    /**
     * @param bool $configValue
     * @param bool $expectedResult
     * @dataProvider isOutputEnabledGenericConfigPathDataProvider
     */
    public function testIsOutputEnabledGenericConfigPath($configValue, $expectedResult)
    {
        $this->_moduleList->expects($this->any())->method('getModule')->will(
            $this->returnValue(array('name' => 'Module_EnabledOne'))
        );
        $this->_outputConfig
            ->expects($this->once())
            ->method('isEnabled')
            ->with('Module_EnabledOne')
            ->will($this->returnValue($configValue))
        ;
        $this->assertEquals($expectedResult, $this->_model->isOutputEnabled('Module_EnabledOne'));
    }

    public function isOutputEnabledGenericConfigPathDataProvider()
    {
        return array(
            'output disabled'   => array(true, false),
            'output enabled'    => array(false, true),
        );
    }

    /**
     * @param bool $configValue
     * @param string $moduleName
     * @param bool $expectedResult
     * @dataProvider isOutputEnabledCustomConfigPathDataProvider
     */
    public function testIsOutputEnabledCustomConfigPath($configValue, $moduleName, $expectedResult)
    {
        $this->_moduleList->expects($this->any())->method('getModule')->will(
            $this->returnValue(array('name' => $moduleName))
        );
        $this->_outputConfig
            ->expects($this->at(0))
            ->method('getFlag')
            ->with(self::XML_PATH_OUTPUT_ENABLED)
            ->will($this->returnValue($configValue))
        ;
        $this->assertEquals($expectedResult, $this->_model->isOutputEnabled($moduleName));
    }

    public function isOutputEnabledCustomConfigPathDataProvider()
    {
        return array(
            'path literal, output disabled'     => array(false, 'Module_DisabledOutputOne', false),
            'path literal, output enabled'      => array(true, 'Module_DisabledOutputOne', true),
            'path constant, output disabled'    => array(false, 'Module_DisabledOutputTwo', false),
            'path constant, output enabled'     => array(true, 'Module_DisabledOutputTwo', true),
        );
    }
}