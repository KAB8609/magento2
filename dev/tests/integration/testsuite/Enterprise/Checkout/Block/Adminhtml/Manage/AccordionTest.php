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

class Enterprise_Checkout_Block_Adminhtml_Manage_AccordionTest extends Mage_Backend_Area_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_Checkout_Block_Adminhtml_Manage_Accordion */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        Mage::getConfig()->setCurrentAreaCode(Mage::helper("Mage_Backend_Helper_Data")->getAreaCode());
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Enterprise_Checkout_Block_Adminhtml_Manage_Accordion');
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_layout = null;
        Mage::getConfig()->setCurrentAreaCode(null);
    }

    public function testToHtml()
    {
        $this->_initAcl();
        $parentName = $this->_block->getNameInLayout();
        $this->_block->setArea('adminhtml');

        // set first child - block
        $title = 'Block 1';
        $url = 'http://content.url.1/';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'block1', $parentName)
            ->setHeaderText($title)
            ->setData('content_url', $url);

        // set second child - container
        $containerName = 'container';
        $this->_layout->addContainer($containerName, 'Container', array(), $parentName);
        $containerText = 'Block in container';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'container_block', $containerName)->setText($containerText);

        // set third child - block
        $titleOne = 'Block 2';
        $blockContent = 'Block 2 Text';
        $this->_layout->addBlock('Mage_Core_Block_Text', 'block2', $parentName)
            ->setHeaderText($titleOne)
            ->setText($blockContent);

        $html = $this->_block->toHtml();
        $this->assertContains($title, $html);
        $this->assertContains($url, $html);
        $this->assertNotContains($containerText, $html);
        $this->assertContains($titleOne, $html);
        $this->assertContains($blockContent, $html);
    }

    /**
     * Substitutes real ACL object for mocked one to make it always return TRUE
     */
    protected function _initAcl()
    {
        $user = Mage::getModel('Mage_User_Model_User');
        $user->setId(1)->setRole(true);
        Mage::getSingleton('Mage_Backend_Model_Auth_Session')->setUpdatedAt(time())->setUser($user);
        Mage::getSingleton(
            'Mage_Core_Model_Authorization', array(
                'data' => array('policy' => new Magento_Authorization_Policy_Default())
        ));
    }
}
