<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Model_Asset_GroupedCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Model_Asset_GroupedCollection
     */
    protected $_object;

    /**
     * @var Magento_Core_Model_Page_Asset_AssetInterface
     */
    protected $_asset;

    protected function setUp()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager
            ->expects($this->any())
            ->method('create')
            ->with('Magento_Page_Model_Asset_PropertyGroup')
            ->will($this->returnCallback(array($this, 'createAssetGroup')))
        ;
        $this->_object = new Magento_Page_Model_Asset_GroupedCollection($objectManager);
        $this->_asset = new Magento_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/test.css');
        $this->_object->add('asset', $this->_asset);
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_asset = null;
    }

    /**
     * Return newly created asset group. Used as a stub for object manger's creation operation.
     *
     * @param string $class
     * @param array $arguments
     * @return Magento_Page_Model_Asset_PropertyGroup
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createAssetGroup($class, array $arguments)
    {
        return new Magento_Page_Model_Asset_PropertyGroup($arguments['properties']);
    }

    /**
     * Assert that actual asset groups equal to expected ones
     *
     * @param array $expectedGroups
     * @param array $actualGroupObjects
     */
    protected function _assertGroups(array $expectedGroups, array $actualGroupObjects)
    {
        $this->assertInternalType('array', $actualGroupObjects);
        $actualGroups = array();
        /** @var $actualGroup Magento_Page_Model_Asset_PropertyGroup */
        foreach ($actualGroupObjects as $actualGroup) {
            $this->assertInstanceOf('Magento_Page_Model_Asset_PropertyGroup', $actualGroup);
            $actualGroups[] = array(
                'properties' => $actualGroup->getProperties(),
                'assets' => $actualGroup->getAll(),
            );
        }
        $this->assertEquals($expectedGroups, $actualGroups);
    }

    public function testAdd()
    {
        $assetNew = new Magento_Core_Model_Page_Asset_Remote('http://127.0.0.1/magento/test_new.css');
        $this->_object->add('asset_new', $assetNew, array('test_property' => 'test_value'));
        $this->assertEquals(array('asset' => $this->_asset, 'asset_new' => $assetNew), $this->_object->getAll());
    }

    public function testRemove()
    {
        $this->_object->remove('asset');
        $this->assertEquals(array(), $this->_object->getAll());
    }

    public function testGetGroups()
    {
        $cssAsset = new Magento_Core_Model_Page_Asset_Remote('http://127.0.0.1/style.css', 'css');
        $jsAsset = new Magento_Core_Model_Page_Asset_Remote('http://127.0.0.1/script.js', 'js');
        $jsAssetAllowingMerge = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_MergeableInterface');
        $jsAssetAllowingMerge->expects($this->any())->method('getContentType')->will($this->returnValue('js'));

        // assets with identical properties should be grouped together
        $this->_object->add('css_asset_one', $cssAsset, array('property' => 'test_value'));
        $this->_object->add('css_asset_two', $cssAsset, array('property' => 'test_value'));

        // assets with different properties should go to different groups
        $this->_object->add('css_asset_three', $cssAsset, array('property' => 'different_value'));
        $this->_object->add('js_asset_one', $jsAsset, array('property' => 'test_value'));

        // assets with identical properties in a different order should be grouped
        $this->_object->add('js_asset_two', $jsAsset, array('property1' => 'value1', 'property2' => 'value2'));
        $this->_object->add('js_asset_three', $jsAsset, array('property2' => 'value2', 'property1' => 'value1'));

        // assets allowing merge should go to separate group regardless of having identical properties
        $this->_object->add('asset_allowing_merge', $jsAssetAllowingMerge, array('property' => 'test_value'));

        $expectedGroups = array(
            array(
                'properties' => array('content_type' => 'unknown', 'can_merge' => false),
                'assets' => array('asset' => $this->_asset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'css', 'can_merge' => false),
                'assets' => array('css_asset_one' => $cssAsset, 'css_asset_two' => $cssAsset),
            ),
            array(
                'properties' => array('property' => 'different_value', 'content_type' => 'css', 'can_merge' => false),
                'assets' => array('css_asset_three' => $cssAsset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'js', 'can_merge' => false),
                'assets' => array('js_asset_one' => $jsAsset),
            ),
            array(
                'properties' => array(
                    'property1' => 'value1', 'property2' => 'value2', 'content_type' => 'js', 'can_merge' => false,
                ),
                'assets' => array('js_asset_two' => $jsAsset, 'js_asset_three' => $jsAsset),
            ),
            array(
                'properties' => array('property' => 'test_value', 'content_type' => 'js', 'can_merge' => true),
                'assets' => array('asset_allowing_merge' => $jsAssetAllowingMerge),
            ),
        );

        $this->_assertGroups($expectedGroups, $this->_object->getGroups());
    }
}