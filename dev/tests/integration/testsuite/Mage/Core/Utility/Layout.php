<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core layout utility
 */
class Mage_Core_Utility_Layout extends Magento_Test_Utility_Abstract
{
    /**
     * Retrieve new layout update model instance with XML data from a fixture file
     *
     * @param string $layoutUpdatesFile
     * @return Mage_Core_Model_Layout_Update|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutUpdateFromFixture($layoutUpdatesFile)
    {
        $layoutUpdate = $this->_testCase->getMock(
            'Mage_Core_Model_Layout_Update', array('getFileLayoutUpdatesXml', 'asSimplexml')
        );
        $layoutUpdatesXml = simplexml_load_file($layoutUpdatesFile, $layoutUpdate->getElementClass());
        $layoutUpdate->expects(PHPUnit_Framework_TestCase::any())
            ->method('getFileLayoutUpdatesXml')
            ->will(PHPUnit_Framework_TestCase::returnValue($layoutUpdatesXml));
        $layoutUpdate->expects(PHPUnit_Framework_TestCase::any())
            ->method('asSimplexml')
            ->will(PHPUnit_Framework_TestCase::returnValue($layoutUpdatesXml));
        return $layoutUpdate;
    }

    /**
     * Retrieve new layout model instance with layout updates from a fixture file
     *
     * @param string $layoutUpdatesFile
     * @return Mage_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject
     */
    public function getLayoutFromFixture($layoutUpdatesFile)
    {
        $layout = $this->_testCase->getMock('Mage_Core_Model_Layout', array('getUpdate'));
        $layoutUpdate = $this->getLayoutUpdateFromFixture($layoutUpdatesFile);
        $layoutUpdate->asSimplexml();
        $layout->expects(PHPUnit_Framework_TestCase::any())
            ->method('getUpdate')
            ->will(PHPUnit_Framework_TestCase::returnValue($layoutUpdate));
        return $layout;
    }
}
