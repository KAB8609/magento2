<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Stub class name for class under test
     */
    const STUB_CLASS = 'Magento_Catalog_Model_Abstract_Stub';

    /**
     * @var \Magento\Catalog\Model\AbstractModel
     */
    protected $_model;

    /**
     * Flag is stub class was created
     *
     * @var bool
     */
    protected static $_isStubClass = false;

    protected function setUp()
    {
        if (!self::$_isStubClass) {
            $this->getMockForAbstractClass('\Magento\Catalog\Model\AbstractModel', array(), self::STUB_CLASS, false);
            self::$_isStubClass = true;
        }

        $this->_model = Mage::getModel(self::STUB_CLASS);

        $resourceProperty = new ReflectionProperty(get_class($this->_model), '_resourceName');
        $resourceProperty->setAccessible(true);
        $resourceProperty->setValue($this->_model, '\Magento\Catalog\Model\Resource\Product');

        $collectionProperty = new ReflectionProperty(get_class($this->_model), '_collectionName');
        $collectionProperty->setAccessible(true);
        $collectionProperty->setValue($this->_model, '\Magento\Catalog\Model\Resource\Product\Collection');
    }

    /**
     * @covers \Magento\Catalog\Model\AbstractModel::lockAttribute
     * @covers \Magento\Catalog\Model\AbstractModel::unlockAttribute
     * @covers \Magento\Catalog\Model\AbstractModel::unlockAttributes
     * @covers \Magento\Catalog\Model\AbstractModel::getLockedAttributes
     * @covers \Magento\Catalog\Model\AbstractModel::hasLockedAttributes
     * @covers \Magento\Catalog\Model\AbstractModel::isLockedAttribute
     */
    public function testLockedAttributeApi()
    {
        $this->assertEquals(array(), $this->_model->getLockedAttributes());
        $this->assertFalse($this->_model->hasLockedAttributes());
        $this->assertFalse($this->_model->isLockedAttribute('some_code'));

        $this->_model->lockAttribute('code');
        $this->assertTrue($this->_model->isLockedAttribute('code'));
        $this->assertEquals(array('code'), $this->_model->getLockedAttributes());
        $this->assertTrue($this->_model->hasLockedAttributes());

        $this->_model->unlockAttribute('code');
        $this->assertFalse($this->_model->isLockedAttribute('code'));

        $this->_model->lockAttribute('code1');
        $this->_model->lockAttribute('code2');
        $this->_model->unlockAttributes();
        $this->assertEquals(array(), $this->_model->getLockedAttributes());
        $this->assertFalse($this->_model->hasLockedAttributes());
    }

    public function testSetData()
    {
        // locked filter on setting all
        $this->_model->lockAttribute('key1');
        $this->_model->setData(array('key1' => 'value1', 'key2' => 'value2'));
        $this->assertEquals(array('key2' => 'value2'), $this->_model->getData());

        // locked filter per setting one
        $this->_model->setData('key1', 'value1');
        $this->_model->setData('key3', 'value3');
        $this->assertEquals(array('key2' => 'value2', 'key3' => 'value3'), $this->_model->getData());

        // set one with read only
        $this->_model->unlockAttributes()->unsetData();
        $this->_model->setIsReadonly(true);
        $this->_model->setData(uniqid(), uniqid());
        $this->assertEquals(array(), $this->_model->getData());
    }

    public function testUnsetData()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');
        $this->_model->setData($data);

        // unset one locked
        $this->_model->lockAttribute('key1')->unsetData('key1');
        $this->assertEquals($data, $this->_model->getData());

        // unset all with read only
        $this->_model->setIsReadonly(true)->unsetData();
        $this->assertEquals($data, $this->_model->getData());

        // unset all
        $this->_model->unlockAttributes()->setIsReadonly(false)->unsetData();
        $this->assertEquals(array(), $this->_model->getData());
    }

    public function testGetResourceCollection()
    {
        $this->_model->setStoreId(99);
        $collection = $this->_model->getResourceCollection();
        $this->assertInstanceOf('\Magento\Catalog\Model\Resource\Collection\AbstractCollection', $collection);
        $this->assertEquals(99, $collection->getStoreId());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testLoadByAttribute()
    {
        $object = $this->_model->loadByAttribute('sku', 'simple');
        $this->assertNotSame($object, $this->_model);
        $this->assertEquals(1, $object->getId()); // fixture

        $result = $this->_model->loadByAttribute('sku', uniqid()); // specifying wrong attribute code leads to fatal
        $this->assertFalse($result);
    }

    public function testGetStore()
    {
        $store = $this->_model->getStore();
        $this->assertSame($store, Mage::app()->getStore());
    }

    public function testGetWebsiteStoreIds()
    {
        $ids = $this->_model->getWebsiteStoreIds();
        $storeId = Mage::app()->getStore()->getId();
        $this->assertEquals(array($storeId => $storeId), $ids);
    }

    public function testSetGetAttributeDefaultValue()
    {
        $this->assertFalse($this->_model->getAttributeDefaultValue('key'));
        $this->_model->setAttributeDefaultValue('key', 'value');
        $this->assertEquals('value', $this->_model->getAttributeDefaultValue('key'));
    }

    public function testSetGetExistsStoreValueFlag()
    {
        $this->assertFalse($this->_model->getExistsStoreValueFlag('key'));
        $this->_model->setExistsStoreValueFlag('key');
        $this->assertTrue($this->_model->getExistsStoreValueFlag('key'));
    }

    /**
     * @covers \Magento\Catalog\Model\AbstractModel::isDeleteable
     * @covers \Magento\Catalog\Model\AbstractModel::setIsDeleteable
     */
    public function testIsDeleteable()
    {
        $this->assertTrue($this->_model->isDeleteable());
        $this->_model->setIsDeleteable(false);
        $this->assertFalse($this->_model->isDeleteable());
    }

    /**
     * @covers \Magento\Catalog\Model\AbstractModel::isReadonly
     * @covers \Magento\Catalog\Model\AbstractModel::setIsReadonly
     */
    public function testIsReadonly()
    {
        $this->assertFalse($this->_model->isReadonly());
        $this->_model->setIsReadonly(true);
        $this->assertTrue($this->_model->isReadonly());
    }
}
