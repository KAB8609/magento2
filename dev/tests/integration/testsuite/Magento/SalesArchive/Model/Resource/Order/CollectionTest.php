<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_SalesArchive_Model_Resource_Order_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testGetSelectCountSql()
    {
        $countSql = Mage::getModel('Magento_SalesArchive_Model_Resource_Order_Collection')->getSelectCountSql();
        $this->assertInstanceOf('Magento_DB_Select', $countSql);
    }
}