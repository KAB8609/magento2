<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Webservice_Rest_Customer extends Magento_Test_Webservice_Rest_Abstract
{
    protected $_userType = 'customer';

    /**
     * Prepare ACL
     */
    public static function setUpBeforeClass()
    {
        require dirname(__FILE__) . '/../../../../../fixtures/Acl/customer_acl.php';

        parent::setUpBeforeClass();
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('rule', true);
        Magento_TestCase::deleteFixture('attribute', true);
        Magento_Test_Webservice::setFixture('customer_acl_is_prepared', false);

        parent::tearDownAfterClass();
    }
}