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

/**
 * @group module:Enterprise_Pci
 */
class Enterprise_Pci_Model_EncryptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Pci_Model_Encryption
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Enterprise_Pci_Model_Encryption();
    }

    public function testEncryptDecrypt()
    {
        $this->assertEquals('', $this->_model->decrypt($this->_model->encrypt('')));
        $this->assertEquals('test', $this->_model->decrypt($this->_model->encrypt('test')));
    }
}