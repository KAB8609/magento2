<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Catalog/_files/product_simple.php
 * @magentoDataFixture Enterprise/Banner/_files/banner_catalog_rule.php
 */
class Enterprise_Banner_Model_Resource_Catalogrule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    protected $_collection;

    /**
     * @var Enterprise_Banner_Model_Banner
     */
    protected $_banner;

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
        $this->_collection = Mage::getResourceModel('Enterprise_Banner_Model_Resource_Catalogrule_Collection');
        $this->_banner = Mage::getModel('Enterprise_Banner_Model_Banner');
        $this->_banner->load('Test Banner', 'name');
    }

    protected function tearDown()
    {
        $this->_collection = null;
        $this->_banner = null;
    }

    public function testConstructor()
    {
        $this->assertStringEndsWith('enterprise_banner_catalogrule', $this->_collection->getMainTable());
    }

    public function testBannerCatalogrule()
    {
        $this->assertCount(1, $this->_collection->getItems());
        $this->assertEquals(
            $this->_banner->getId(),
            $this->_collection->getFirstItem()->getBannerId()
        );
    }

    public function testAddWebsiteCustomerGroupFilter()
    {
        $this->_collection->addWebsiteCustomerGroupFilter($this->_websiteId, $this->_customerGroupId);
        $this->testBannerCatalogrule();
    }

    /**
     * @dataProvider addWebsiteCustomerGroupFilterWrongDataDataProvider
     */
    public function testAddWebsiteCustomerGroupFilterWrongData($websiteId, $customerGroupId)
    {
        $this->assertCount(1, $this->_collection->getItems());
        $this->assertEmpty(
            $this->_collection->addWebsiteCustomerGroupFilter($websiteId, $customerGroupId)->getAllIds()
        );
    }

    /**
     * @return array
     */
    public function addWebsiteCustomerGroupFilterWrongDataDataProvider()
    {
        return array(
            'wrong website' => array($this->_websiteId + 1, $this->_customerGroupId),
            'wrong customer group' => array($this->_websiteId, $this->_customerGroupId + 1)
        );
    }
}
