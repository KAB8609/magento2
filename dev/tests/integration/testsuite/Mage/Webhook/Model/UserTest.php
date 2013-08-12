<?php
/**
 * Mage_Webhook_Model_User
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webhook
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_UserTest extends PHPUnit_Framework_TestCase
{
    public function testGetSharedSecret()
    {
        $webapiUserId = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')
            ->setSecret('secret')
            ->save()
            ->getId();
        $user = Mage::getObjectManager()->create('Mage_Webhook_Model_User', array('webapiUserId' => $webapiUserId));
        $this->assertEquals('secret', $user->getSharedSecret());
    }
}