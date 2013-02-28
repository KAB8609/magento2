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

class Mage_Core_Model_Resource_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Layout_Update
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getResourceModel('Mage_Core_Model_Resource_Layout_Update');
    }

    public function testFetchUpdatesByHandle()
    {
        $this->assertEmpty($this->_model->fetchUpdatesByHandle('test', array('test' => 'test')));
    }
}
