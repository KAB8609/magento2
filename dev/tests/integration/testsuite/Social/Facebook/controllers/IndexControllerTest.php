<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Social_Facebook
 */
class Social_Facebook_IndexControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoConfigFixture current_store facebook/config/enabled 1
     */
    public function testSuggestAction()
    {
        $this->getRequest()->setParam('id', '1');
        $this->dispatch('facebook/index/page');
        $this->assertContains(
            '<meta property="og:title" content="Simple Product" />',
            $this->getResponse()->getBody()
        );
    }
}
