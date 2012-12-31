<?php
/**
 * Core module API tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento info Api tests
 */
class Mage_Core_MagentoTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test magento magento info retrieving
     */
    public function testInfo()
    {
        $magentoInfo = Magento_Test_Helper_Api::call($this, 'magentoInfo');
        $this->assertNotEmpty($magentoInfo['magento_version']);
        $this->assertNotEmpty($magentoInfo['magento_edition']);
        $this->assertEquals(Mage::getEdition(), $magentoInfo['magento_edition']);
    }
}
