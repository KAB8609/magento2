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

/**
 * Test class for Magento_Cms_Controller_Page.
 */
class Magento_Cms_Controller_PageTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testViewAction()
    {
        $this->dispatch('/about-magento-demo-store');
        $this->assertContains('About Magento Store', $this->getResponse()->getBody());
    }
}