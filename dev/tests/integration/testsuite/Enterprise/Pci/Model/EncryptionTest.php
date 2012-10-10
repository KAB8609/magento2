<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Pci
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Pci_Model_EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Pci_Model_Encryption
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Enterprise_Pci_Model_Encryption');
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
