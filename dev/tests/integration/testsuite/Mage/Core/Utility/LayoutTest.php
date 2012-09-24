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

class Mage_Core_Utility_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Utility_Layout
     */
    protected $_utility;

    public static function setUpBeforeClass()
    {
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_utility = new Mage_Core_Utility_Layout($this);
    }

    protected function tearDown()
    {
        $this->_utility = null;
    }

    /**
     * Assert that the actual layout update instance represents the expected layout update file
     *
     * @param Mage_Core_Model_Layout_Update $actualUpdate
     * @param string $expectedUpdateFile
     */
    protected function _assertLayoutUpdate($actualUpdate, $expectedUpdateFile)
    {
        $this->assertInstanceOf('Mage_Core_Model_Layout_Update', $actualUpdate);

        $layoutUpdateXml = $actualUpdate->getFileLayoutUpdatesXml();
        $this->assertInstanceOf('Mage_Core_Model_Layout_Element', $layoutUpdateXml);
        $this->assertXmlStringEqualsXmlFile($expectedUpdateFile, $layoutUpdateXml->asNiceXml());
    }

    public function testGetLayoutUpdateFromFixture()
    {
        $layoutUpdateFile = __DIR__ . '/_files/_layout_update.xml';
        $layoutUpdate = $this->_utility->getLayoutUpdateFromFixture($layoutUpdateFile);
        $this->_assertLayoutUpdate($layoutUpdate, $layoutUpdateFile);
    }

    public function testGetLayoutFromFixture()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $layoutUpdateFile = __DIR__ . '/_files/_layout_update.xml';
        $layout = $this->_utility->getLayoutFromFixture($layoutUpdateFile);
        $this->assertInstanceOf('Mage_Core_Model_Layout', $layout);
        $this->_assertLayoutUpdate($layout->getUpdate(), $layoutUpdateFile);
    }
}
