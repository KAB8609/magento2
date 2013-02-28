<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftCard_Model_Observer
     */
    protected $_model;

    /**
     * Test that dependency injections passed to the constructor will not be duplicated in _data property
     */
    public function testConstructorValidArguments()
    {
        $context = new Mage_Core_Model_Context(
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false)
        );
        $this->_model = new Enterprise_GiftCard_Model_Observer(
            $context,
            null,
            null,
            array(
            'email_template_model' => $this->getMock('Mage_Core_Model_Email_Template', array(), array(), '', false),
            'custom_field'         => 'custom_value',
        ));
        $this->assertEquals(array('custom_field' => 'custom_value'), $this->_model->getData());
    }

    /**
     * Test that only valid model instance can be passed to the constructor
     *
     * @expectedException InvalidArgumentException
     */
    public function testConstructorInvalidArgument()
    {
        $context = new Mage_Core_Model_Context(
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false)
        );
        $this->_model = new Enterprise_GiftCard_Model_Observer(
            $context,
            null,
            null,
            array('email_template_model' => new stdClass())
        );
    }
}
