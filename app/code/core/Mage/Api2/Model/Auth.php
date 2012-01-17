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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API User authentication model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth
{
    /**
     * Figure out API user type, create user model instance
     *
     * @param Mage_Api2_Model_Request $request
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    public function authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('api2');
        /** @var $authAdapter Mage_Api2_Model_Auth_Adapter */
        $authAdapter = Mage::getModel('api2/auth_adapter');

        $userTypes = $helper->getUserTypes();
        $userType  = $authAdapter->getUserType($request);

        if (!isset($userTypes[$userType])) {
            throw new Exception('Invalid user type or type is not allowed');
        }
        /** @var $userModel Mage_Api2_Model_Auth_User_Abstract */
        $userModel = Mage::getModel($userTypes[$userType]);

        if (!$userModel instanceof Mage_Api2_Model_Auth_User_Abstract) {
            throw new Exception('User model must to extend Mage_Api2_Model_Auth_User_Abstract');
        }
        $userModel->setType($userType);

        return $userModel;
    }
}
