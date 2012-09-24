<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Translate_StringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Translate_String
     */
    protected $_model;

    public function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->_model = new Mage_Core_Model_Translate_String();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Mage_Core_Model_Resource_Translate_String', $this->_model->getResource());
    }

    public function testSetGetString()
    {
        $expectedString = __METHOD__;
        $this->_model->setString($expectedString);
        $actualString = $this->_model->getString();
        $this->assertEquals($expectedString, $actualString);
    }
}
