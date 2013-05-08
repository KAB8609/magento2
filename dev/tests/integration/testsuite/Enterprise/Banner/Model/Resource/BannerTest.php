<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Model_Resource_BannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Model_Resource_Banner
     */
    private $_resourceModel;

    /**
     * @var int
     */
    protected $_websiteId = 1;

    /**
     * @var int
     */
    protected $_customerGroupId = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;

    protected function setUp()
    {
        $this->_resourceModel = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Banner');
    }

    protected function tearDown()
    {
        $this->_resourceModel = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoDataFixture Mage/CatalogRule/_files/catalog_rule_10_off_not_logged.php
     * @magentoDataFixture Enterprise/Banner/_files/banner.php
     */
    public function testGetCatalogRuleRelatedBannerIdsNoBannerConnected()
    {
        $this->assertEmpty(
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($this->_websiteId, $this->_customerGroupId)
        );
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_catalog_rule.php
     */
    public function testGetCatalogRuleRelatedBannerIds() {
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Test Banner', 'name');

        $this->assertSame(
            array($banner->getId()),
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($this->_websiteId, $this->_customerGroupId)
        );
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_catalog_rule.php
     * @dataProvider getCatalogRuleRelatedBannerIdsWrongDataDataProvider
     */
    public function testGetCatalogRuleRelatedBannerIdsWrongData($websiteId, $customerGroupId)
    {
        $this->assertEmpty(
            $this->_resourceModel->getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
        );
    }

    /**
     * @return array
     */
    public function getCatalogRuleRelatedBannerIdsWrongDataDataProvider() {
        return array(
            'wrong website' => array($this->_websiteId + 1, $this->_customerGroupId),
            'wrong customer group' => array($this->_websiteId, $this->_customerGroupId + 1)
        );
    }

    /**
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIds()
    {
        /** @var Mage_SalesRule_Model_Rule $rule */
        $rule = Mage::getModel('Mage_SalesRule_Model_Rule');
        $rule->load('40% Off on Large Orders', 'name');

        /** @var Enterprise_Banner_Model_Banner $banner */
        $banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $banner->load('Get from 40% to 50% Off on Large Orders', 'name');

        $this->assertEquals(
            array($banner->getId()), $this->_resourceModel->getSalesRuleRelatedBannerIds(array($rule->getId()))
        );
    }

    /**
     * @magentoDataFixture Enterprise/Banner/_files/banner_enabled_40_to_50_percent_off.php
     * @magentoDataFixture Enterprise/Banner/_files/banner_disabled_40_percent_off.php
     */
    public function testGetSalesRuleRelatedBannerIdsNoRules()
    {
        $this->assertEmpty($this->_resourceModel->getSalesRuleRelatedBannerIds(array()));
    }
}
