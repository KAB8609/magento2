<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Page_Asset_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Page\Asset\Collection
     */
    protected $_object;

    /**
     * @var \Magento\Core\Model\Page\Asset\AssetInterface
     */
    protected $_asset;

    protected function setUp()
    {
        $this->_object = new \Magento\Core\Model\Page\Asset\Collection();
        $this->_asset = new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/test.css');
        $this->_object->add('asset', $this->_asset);
    }

    public function testAdd()
    {
        $assetNew = new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/test.js');
        $this->_object->add('asset_new', $assetNew);
        $this->assertSame(array('asset' => $this->_asset, 'asset_new' => $assetNew), $this->_object->getAll());
    }

    public function testHas()
    {
        $this->assertTrue($this->_object->has('asset'));
        $this->assertFalse($this->_object->has('non_existing_asset'));
    }

    public function testAddSameInstance()
    {
        $this->_object->add('asset_clone', $this->_asset);
        $this->assertSame(array('asset' => $this->_asset, 'asset_clone' => $this->_asset), $this->_object->getAll());
    }

    public function testAddOverrideExisting()
    {
        $assetOverridden = new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/magento/test_overridden.css');
        $this->_object->add('asset', $assetOverridden);
        $this->assertSame(array('asset' => $assetOverridden), $this->_object->getAll());
    }

    public function testRemove()
    {
        $this->_object->remove('asset');
        $this->assertSame(array(), $this->_object->getAll());
    }

    public function testGetAll()
    {
        $this->assertSame(array('asset' => $this->_asset), $this->_object->getAll());
    }
}
