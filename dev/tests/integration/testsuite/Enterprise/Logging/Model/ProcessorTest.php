<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Enterprise logging processor
 */
class Enterprise_Logging_Model_ProcessorTest extends Magento_Test_TestCase_ControllerAbstract
{
    public static function userAndRoleFixture()
    {
        $user = Mage::getModel('Mage_User_Model_User');
        $user->setUsername('newuser')
            ->setFirstname('first_name')
            ->setLastname('last_name')
            ->setPassword('password1')
            ->setEmail('newuser@example.com')
            ->setRoleId(1)
            ->save();

        $role = Mage::getModel('Mage_User_Model_Role');
        $role->setName('newrole')
            ->save();
    }

    /**
     * Test that configured admin actions are properly logged
     *
     * @param string $url
     * @param string $action
     * @param array $post
     * @dataProvider adminActionDataProvider
     * @magentoDataFixture userAndRoleFixture
     */
    public function testLoggingProcessorLogsAction($url, $action, array $post = array())
    {
        $this->markTestIncomplete('MAGETWO-6891');
        Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $collection = Mage::getModel('Enterprise_Logging_Model_Event')->getCollection();
        $eventCount = count($collection);

        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);

        $this->getRequest()->setServer(array('REQUEST_METHOD' => 'POST'));
        $this->getRequest()->setPost(
            array_merge($post, array('form_key' => Mage::getSingleton('Mage_Core_Model_Session')->getFormKey()))
        );
        $this->dispatch($url);
        $collection = Mage::getModel('Enterprise_Logging_Model_Event')->getCollection();
        $this->assertEquals($eventCount + 1, count($collection), $action . ' event wasn\'t logged');
        $lastEvent = $collection->getLastItem();
        $this->assertEquals($action, $lastEvent['action']);
    }

    public function adminActionDataProvider()
    {
        return array(
            array('backend/admin/user/edit/user_id/2', 'view'),
            array(
                'backend/admin/user/save', 'save',
                array(
                    'email' => 'newuser@ebay.com',
                    'roles[]' => 1,
                    'username' => 'newuser',
                    'password' => 'password'
                )
            ),
            array('backend/admin/user/delete/user_id/2', 'delete'),
            array('backend/admin/user_role/editrole/rid/2', 'view'),
            array(
                'backend/admin/user_role/saverole', 'save',
                array(
                    'rolename' => 'newrole2',
                    'gws_is_all' => '1'
                )
            ),
            array('backend/admin/user_role/delete/rid/2', 'delete'),
            array('backend/admin/tax_class/ajaxDelete', 'delete', array('class_id' => 1, 'isAjax' => true)),
            array('backend/admin/tax_class/ajaxSave', 'save',
                array(
                    'class_id' => null,
                    'class_name' => 'test',
                    'class_type' => 'PRODUCT',
                    'isAjax' => true,
                )
            )
        );
    }
}
