<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cms_Helper_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        $arguments = array(
            'request' => new Magento_Test_Request(),
            'response' => new Magento_Test_Response()
        );
        $context = Mage::getModel('Magento_Core_Controller_Varien_Action_Context', $arguments);
        $page = Mage::getSingleton('Magento_Cms_Model_Page');
        $page->load('page_design_blank', 'identifier'); // fixture
        /** @var $pageHelper Magento_Cms_Helper_Page */
        $pageHelper = Mage::helper('Magento_Cms_Helper_Page');
        $result = $pageHelper->renderPage(
            Mage::getModel('Magento_Core_Controller_Front_Action', array('context' => $context)),
            $page->getId()
        );
        $this->assertEquals('magento_blank', Mage::getDesign()->getDesignTheme()->getThemePath());
        $this->assertTrue($result);
    }
}