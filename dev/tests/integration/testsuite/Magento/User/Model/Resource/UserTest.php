<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Model_Resource_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_User_Model_Resource_User */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_User_Model_Resource_User');
    }

    public function testCountAll()
    {
        $this->assertSame(1, $this->_model->countAll());
    }

    public function testGetValidationRulesBeforeSave()
    {
        $rules = $this->_model->getValidationRulesBeforeSave();
        $this->assertInstanceOf('Zend_Validate_Interface', $rules);
    }
}
