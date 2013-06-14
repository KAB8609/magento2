<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Model_Resource_Rules_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Resource_Rules_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel('Mage_User_Model_Resource_Rules_Collection');
    }

    public function testGetByRoles()
    {
        $user = Mage::getModel('Mage_User_Model_User');
        $user->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        $this->_collection->getByRoles($user->getRole()->getId());

        $where = $this->_collection->getSelect()->getPart(Zend_Db_Select::WHERE);
        /** @var Zend_Db_Adapter_Abstract $adapter */
        $adapter = $this->_collection->getConnection();
        $quote = $adapter->getQuoteIdentifierSymbol();
        $this->assertContains("({$quote}role_id{$quote} = '" . $user->getRole()->getId()."')", $where);
    }

    public function testAddSortByLength()
    {
        $this->_collection->addSortByLength();

        $order = $this->_collection->getSelect()->getPart(Zend_Db_Select::ORDER);
        $this->assertContains(array('length', 'DESC'), $order);
    }
}
