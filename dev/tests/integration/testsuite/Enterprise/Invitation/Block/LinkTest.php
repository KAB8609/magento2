<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Invitation
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Invitation_Block_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Invitation_Block_Link
     */
    protected $_block;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $this->_block = Mage::getModel('Enterprise_Invitation_Block_Link');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @magentoConfigFixture current_store enterprise_invitation/general/enabled 1
     * @magentoConfigFixture current_store enterprise_invitation/general/enabled_on_front 1
     * magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testAddAccountLink()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $layout = Mage::app()->getLayout();
        $this->_block->setLayout($layout);
        $layout->addBlock('Mage_Page_Block_Template_Links', 'account.links');

        /* @var Mage_Page_Block_Template_Links $links */
        $links = $layout->getBlock('account.links');
        $this->assertEmpty($links->getLinks());

        $this->_block->addAccountLink();
        $this->assertEmpty($links->getLinks());

        Mage::getSingleton('Mage_Customer_Model_Session')->login('customer@example.com', 'password');
        $this->_block->addAccountLink();
        $links = $links->getLinks();
        $this->assertNotEmpty($links);
        $this->assertEquals('Send Invitations', $links[1]->getLabel());
    }
}
