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
class Core_MagentoTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Test magento magento info retrieving
     */
    public function testInfo()
    {
        $magentoInfo = $this->call('magento.info');
        $this->assertNotEmpty($magentoInfo['magento_version']);
        $this->assertNotEmpty($magentoInfo['magento_edition']);
        $this->assertEquals(Mage::getEdition(), $magentoInfo['magento_edition']);
    }
}
