<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_XmlConnect
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_XmlConnect_Model_TabsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (Mage::registry('current_app') === null) {
            $application = Mage::getModel('Mage_XmlConnect_Model_Application')->setType(
                Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE
            );
            Mage::register('current_app', $application);
        }
    }

    public function testGetRenderTabs()
    {
        $this->markTestIncomplete('MAGETWO-4738');
        $model = Mage::getModel('Mage_XmlConnect_Model_Tabs', array('data' => false));
        $tabs = $model->getRenderTabs();
        $this->assertInternalType('array', $tabs);
        $this->assertNotEmpty($tabs);
        foreach ($tabs as $tab) {
            $this->assertArrayHasKey('label', $tab);
            $this->assertArrayHasKey('image', $tab);
            $this->assertArrayHasKey('action', $tab);
            $this->assertNotEmpty($tab['label']);
            $this->assertNotEmpty($tab['image']);
            $this->assertStringMatchesFormat(
                'http://%s/pub/media/theme/%s/%s/%s/%s/Mage_XmlConnect/images/%s.png', $tab['image']
            );
            $this->assertNotEmpty($tab['action']);
        }
    }

    public function testGetRenderTabsJson()
    {
        $model = Mage::getModel('Mage_XmlConnect_Model_Tabs',
            array('data' => '{"enabledTabs":[{"image":"images/tab_account.png"}]}')
        );
        $tabs = $model->getRenderTabs();
        $this->assertInternalType('array', $tabs);
        $this->assertNotEmpty($tabs);
        foreach ($tabs as $tab) {
            $this->assertInternalType('object', $tab);
            $this->assertObjectHasAttribute('image', $tab);
            $this->assertEquals('images/tab_account.png', $tab->image);
        }
    }
}
