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

class Mage_Core_Controller_Varien_Router_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Controller_Varien_Router_Abstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Core_Controller_Varien_Router_Abstract');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testGetSetFront()
    {
        $expected = Mage::getModel('Mage_Core_Controller_Varien_Front');
        $this->assertNull($this->_model->getFront());
        $this->_model->setFront($expected);
        $this->assertSame($expected, $this->_model->getFront());
    }
}
