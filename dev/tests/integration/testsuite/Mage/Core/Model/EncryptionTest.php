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

class Mage_Core_Model_EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Encryption
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Encryption();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testEncryptDecrypt()
    {
        $this->assertEquals('', $this->_model->decrypt($this->_model->encrypt('')));
        $this->assertEquals('test', $this->_model->decrypt($this->_model->encrypt('test')));
    }
}
