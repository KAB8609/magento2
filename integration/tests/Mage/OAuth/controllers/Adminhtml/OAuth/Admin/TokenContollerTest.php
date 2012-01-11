<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test model admin Authorized Tokens controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_OAuth_Adminhtml_OAuth_Admin_TokenControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Get token data
     *
     * @return array
     */
    protected function _getFixtureModels()
    {
        return require realpath(dirname(__FILE__) . '/../../../..') .
                '/Model/_fixtures/tokenConsumerCreate.php';
    }

    /**
     * Test update revoke status
     *
     */
    public function testRevokeAction()
    {
        //generate test items
        $models = $this->_getFixtureModels();

        $redirectUrl  = 'admin/oAuth_admin_token/index';
        $dispatchPath = 'admin/oAuth_admin_token/revoke';

        $models = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds = array();
        /** @var $item Mage_OAuth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);

        $message                = 'Token is not updated.';
        $messageMustNotUpdated  = 'Token is updated but it must be not.';

        foreach (array(0, 1) as $revoked) {
            $this->getRequest()->setParam('status', $revoked);
            Mage::unregister('application_params');
            $this->dispatch($dispatchPath);
            $this->assertRedirectMatch($redirectUrl);

            /** @var $item Mage_OAuth_Model_Token */
            foreach ($models as $item) {
                $mustChange = $item->getAdminId() && $item->getType() == Mage_OAuth_Model_Token::TYPE_ACCESS;
                $revokedTest = $mustChange ? $revoked : $item->getRevoked();
                $item->load($item->getId());
                $this->assertEquals($revokedTest, $item->getRevoked(), $mustChange ? $message : $messageMustNotUpdated);
            }
        }
    }


    /**
     * Test delete action
     */
    public function testDeleteAction()
    {
        //generate test items
        $models = $this->_getFixtureModels();

        $redirectUrl  = 'admin/oAuth_admin_token/index';
        $dispatchPath = 'admin/oAuth_admin_token/delete';

        $models = array_merge($models['token']['customer'], $models['token']['admin']);
        $tokenIds = array();
        /** @var $item Mage_OAuth_Model_Token */
        foreach ($models as $item) {
            $tokenIds[] = $item->getId();
        }

        $this->loginToAdmin();
        $this->getRequest()->setParam('items', $tokenIds);

        $message                = 'Token is not deleted.';
        $messageMustNotUpdated  = 'Token is deleted but it must be not.';
        Mage::unregister('application_params');
        $this->dispatch($dispatchPath);
        $this->assertRedirectMatch($redirectUrl);

        /** @var $item Mage_OAuth_Model_Token */
        foreach ($models as $item) {
            $mustChange = $item->getAdminId() && $item->getType() == Mage_OAuth_Model_Token::TYPE_ACCESS;
            $id = $item->getId();
            $item->setData(array());
            $item->load($id);
            $this->assertEquals(
                $item->getId(),
                $mustChange ? null : $id,
                $mustChange ? $message : $messageMustNotUpdated);
        }
    }
}
