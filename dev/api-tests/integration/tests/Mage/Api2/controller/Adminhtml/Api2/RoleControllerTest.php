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
 * Test model admin api role controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Api2_Adminhtml_Api2_RoleControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Test role correctly saved
     */
    public function testRoleSaveCreate()
    {
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $roleName = uniqid('role_');


        try {
            $this->loginToAdmin();
            $this->getRequest()->setParam('role_name', $roleName);
            $this->dispatch('admin/api2_role/save');
        } catch (Exception $e) {
            throw $e;
        }

        $role->load($roleName, 'role_name');
        $this->assertTrue($role->getId()>0);

        $role->delete();
    }

    /**
     * Test role correctly saved
     */
    public function testRoleSaveUpdate()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();
        $this->setFixture('role', $role);

        $roleName2 = uniqid('role_');

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParam('role_name', $roleName2);
            $this->getRequest()->setParam('id', $role->getId());
            $this->dispatch('admin/api2_role/save');
        } catch (Exception $e) {
            throw $e;
        }

        $role->load($role->getId());

        $this->assertEquals($roleName2, $role->getRoleName());
    }

    /**
     * Test role is rendered
     */
    public function testRoleGrid()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();
        $this->setFixture('role', $role);

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParam('role_name', $roleName);
            $this->dispatch('admin/api2_role/');
        } catch (Exception $e) {
            throw $e;
        }
        $html = $this->getResponse()->getBody();
        $this->assertContains($roleName, $html);
    }

    /**
     * Test role is deleted correctly
     */
    public function testRoleDelete()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();
        //$this->setFixture('role', $role);

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParam('id', $role->getId());
            $this->dispatch('admin/api2_role/delete');
        } catch (Exception $e) {
            throw $e;
        }

        /** @var $role2 Mage_Api2_Model_Acl_Global_Role */
        $role2 = Mage::getModel('api2/acl_global_role');

        $role2->load($role->getId());
        $this->assertEmpty($role2->getId());
    }

}
