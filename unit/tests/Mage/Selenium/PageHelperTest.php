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
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Unit test for Page helper
 */
class Mage_Selenium_Helper_PageTest extends Mage_PHPUnit_TestCase
{
    /**
     * Testing Mage_Selenium_Helper_Page::validateCurrentPage()
     */
    public function testValidateCurrentPage()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $_pageHelper->validateCurrentPage());
    }

    /**
     * Testing Mage_Selenium_Helper_Page::validationFailed()
     */
    public function testValidationFailed()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_pageHelper->validateCurrentPage();
        $this->assertFalse($_pageHelper->validationFailed());
    }

    /**
     * Testing Mage_Selenium_Helper_Page::setApplicationHelper()
     */
    public function testSetApplicationHelper()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_Page', $_pageHelper->setApplicationHelper($_suitHelper));
    }

    /**
     * Testing Mage_Selenium_Helper_Page::getPageUrl()
     */
    public function testGetPageUrl()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertStringEndsWith('/control/permissions_user/', $_pageHelper->getPageUrl('manage_admin_users'));
        //$this->assertFalse($_pageHelper->getPageUrl(''));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() on uninitialized object
     *
     * @expectedException Mage_Selenium_Exception
     */
    public function testGetPageUrlUninitializedException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $this->assertStringEndsWith('/control/permissions_user/', $_pageHelper->getPageUrl('manage_admin_users'));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() wrong Area
     *
     * @expectedException OutOfRangeException
     */
    public function testGetPageUrlWrongAreaException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin-bla-bla-bla');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertFalse($_pageHelper->getPageUrl('some_page'));
    }

    /**
     * Test Mage_Selenium_Helper_Page::getPageUrl() wrong url
     *
     * @expectedException Mage_Selenium_Exception
     */
    public function testGetPageUrlWrongUrlException()
    {
        $_pageHelper = new Mage_Selenium_Helper_Page($this->_config);
        $_suitHelper = new Mage_Selenium_Helper_Application($this->_config);
        $_suitHelper->setArea('admin');
        $_pageHelper->setApplicationHelper($_suitHelper);

        $this->assertFalse($_pageHelper->getPageUrl('some_page'));
    }
}