<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Enterprise_Checkout
     * @subpackage  integration_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * @group module:Enterprise_Checkout
     */
class Enterprise_Checkout_Block_Adminhtml_Manage_AccordionTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = new Mage_Core_Model_Layout;
        $this->_block = $this->_layout->createBlock('Enterprise_Checkout_Block_Adminhtml_Manage_Accordion');
    }

    public function testToHtml()
    {
        $this->_initAcl(true);
        $parentName = $this->_block->getNameInLayout();
        $this->_block->setArea('adminhtml');

        // set first child - block
        $title1 = 'Block 1';
        $url1 = 'http://content.url.1/';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'block1', $parentName)
            ->setHeaderText($title1)
            ->setData('content_url', $url1);

        // set second child - container
        $containerName = 'container';
        $this->_layout->insertContainer($parentName, $containerName);
        $containerText = 'Block in container';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'container_block', $containerName)->setText($containerText);

        // set third child - block
        $title2 = 'Block 2';
        $blockContent = 'Block 2 Text';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'block2', $parentName)
            ->setHeaderText($title2)
            ->setText($blockContent);

        $html = $this->_block->toHtml();
        $this->assertContains($title1, $html);
        $this->assertContains($url1, $html);
        $this->assertNotContains($containerText, $html);
        $this->assertContains($title2, $html);
        $this->assertContains($blockContent, $html);
    }

    protected function _initAcl($return)
    {
        $user = new Mage_Admin_Model_User;
        $user->setId(1)->setRole(true);
        $acl = $this->getMock('Mage_Admin_Model_Resource_Acl', array('isAllowed'));
        $acl->expects(self::any())
            ->method('isAllowed')
            ->will($this->returnValue($return));
        Mage::getSingleton('Mage_Admin_Model_Session')->setUpdatedAt(time())->setAcl($acl)->setUser($user);
    }
}