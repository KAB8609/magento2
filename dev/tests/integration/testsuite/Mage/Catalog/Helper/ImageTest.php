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

/**
 * @group module:Mage_Catalog
 */
class Mage_Catalog_Helper_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Helper_Image
     */
    protected $_helper;

    /**
     * @var string
     */
    protected static $_fixtureMediaDir = '';

    /**
     * @var string
     */
    protected static $_sampleCachedUrl = '';

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected static $_product;

    public static function setUpBeforeClass()
    {
        // image fixtures
        self::$_fixtureMediaDir = Mage::getSingleton('Mage_Catalog_Model_Product_Media_Config')->getBaseMediaPath();
        mkdir(self::$_fixtureMediaDir . '/m/a', 0777, true);
        $fixtureDir = dirname(dirname(__FILE__)) . '/_files';
        copy("{$fixtureDir}/magento_image.jpg", self::$_fixtureMediaDir . '/m/a/magento_image.jpg');
        copy("{$fixtureDir}/magento_small_image.jpg", self::$_fixtureMediaDir . '/m/a/magento_small_image.jpg');
        copy("{$fixtureDir}/magento_thumbnail.jpg", self::$_fixtureMediaDir . '/m/a/magento_thumbnail.jpg');

        // watermark fixture
        mkdir(self::$_fixtureMediaDir . '/watermark/stores/' . Mage::app()->getStore()->getId(), 0777, true);
        copy("{$fixtureDir}/watermark.jpg",
            self::$_fixtureMediaDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . '/watermark.jpg'
        );

        // sample product with images
        self::$_product = new Mage_Catalog_Model_Product;
        self::$_product
            ->addData(array(
                'image'       => '/m/a/magento_image.jpg',
                'small_image' => '/m/a/magento_small_image.jpg',
                'thumbnail'   => '/m/a/magento_thumbnail.jpg',
            ))
        ;

        // sample image cached URL
        $helper = new Mage_Catalog_Helper_Image;
        self::$_sampleCachedUrl = (string)$helper->init(self::$_product, 'image');
    }

    public static function tearDownAfterClass()
    {
        unlink(self::$_fixtureMediaDir . '/m/a/magento_image.jpg');
        unlink(self::$_fixtureMediaDir . '/m/a/magento_small_image.jpg');
        unlink(self::$_fixtureMediaDir . '/m/a/magento_thumbnail.jpg');
        rmdir(self::$_fixtureMediaDir . '/m/a');
        rmdir(self::$_fixtureMediaDir . '/m');

        unlink(self::$_fixtureMediaDir . '/watermark/stores/' . Mage::app()->getStore()->getId() . '/watermark.jpg');
        rmdir(self::$_fixtureMediaDir . '/watermark/stores/' . Mage::app()->getStore()->getId());
        rmdir(self::$_fixtureMediaDir . '/watermark/stores');
        rmdir(self::$_fixtureMediaDir . '/watermark');

        $images = new Mage_Catalog_Model_Product_Image;
        $images->clearCache();
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_helper = new Mage_Catalog_Helper_Image;
    }

    /**
     * init()
     * __toString()
     * @dataProvider initDataProvider
     */
    public function testInit($imageType, $expectedEnding)
    {
        $this->assertStringEndsWith($expectedEnding, (string)$this->_init($imageType));
    }

    public function initDataProvider()
    {
        return array(
            array('image',       '/m/a/magento_image.jpg'),
            array('small_image', '/m/a/magento_small_image.jpg'),
            array('thumbnail',   '/m/a/magento_thumbnail.jpg'),
        );
    }

    public function testResize()
    {
        $this->assertNotEquals((string)$this->_init()->resize(100), self::$_sampleCachedUrl);
    }

    public function testSetQuality()
    {
        $this->assertNotEquals((string)$this->_init()->setQuality(50), self::$_sampleCachedUrl);
    }

    public function testKeepAspectRatio()
    {
        $this->assertEquals((string)$this->_init()->keepAspectRatio(true), self::$_sampleCachedUrl);
        $this->assertNotEquals((string)$this->_init()->keepAspectRatio(false), self::$_sampleCachedUrl);
    }

    public function testKeepFrame()
    {
        $this->assertEquals((string)$this->_init()->keepFrame(true), self::$_sampleCachedUrl);
        $this->assertNotEquals((string)$this->_init()->keepFrame(false), self::$_sampleCachedUrl);
    }

    public function testKeepTransparency()
    {
        $this->assertEquals((string)$this->_init()->keepTransparency(true), self::$_sampleCachedUrl);
        $this->assertNotEquals((string)$this->_init()->keepTransparency(false), self::$_sampleCachedUrl);
    }

    public function testConstrainOnly()
    {
        $this->assertEquals((string)$this->_init()->constrainOnly(false), self::$_sampleCachedUrl);
        $this->assertNotEquals((string)$this->_init()->constrainOnly(true), self::$_sampleCachedUrl);
    }

    public function testBackgroundColor()
    {
        $rgbArray = (string)$this->_init()->backgroundColor(array(100, 100, 100));
        $rgbArgs = (string)$this->_init()->backgroundColor(100, 100, 100);
        $this->assertEquals($rgbArgs, $rgbArray);
        $this->assertNotEquals($rgbArray, self::$_sampleCachedUrl);
        $this->assertNotEquals($rgbArgs, self::$_sampleCachedUrl);
    }

    public function testRotate()
    {
        $this->assertNotEquals((string)$this->_init()->rotate(15), self::$_sampleCachedUrl);
    }

    public function testWatermark()
    {
        $this->assertNotEquals(
            (string)$this->_init()->watermark('/watermark.jpg', 0, '15x15', 50), self::$_sampleCachedUrl
        );
    }

    /**
     * placeholder()
     * getPlaceholder()
     */
    public function testPlaceholder()
    {
        $this->_init();
        $defaultPlaceholder = $this->_helper->getPlaceholder();
        $this->assertNotEmpty($defaultPlaceholder);

        $placeholder = uniqid() . 'placeholder.png';
        $this->_helper->placeholder($placeholder);
        $this->assertEquals($placeholder, $this->_helper->getPlaceholder());

        $this->assertNotEquals($placeholder, $defaultPlaceholder);
    }

    public function testGetPlaceholder()
    {
        $model = new Mage_Catalog_Model_Product;
        $this->_helper->init($model, 'image');
        $placeholder = $this->_helper->getPlaceholder();
        $this->assertEquals('images/catalog/product/placeholder/image.jpg', $placeholder);

        // test that placeholder doesn't change, once initialized
        $model->setDestinationSubDir('other_image');
        $this->assertEquals($placeholder, $this->_helper->getPlaceholder());
    }

    /**
     * getOriginalWidth()
     * getOriginalHeight()
     * getOriginalSizeArray()
     */
    public function testGetOriginalWidthHeight()
    {
        $this->assertEquals(272, $this->_init()->getOriginalWidth());
        $this->assertEquals(261, $this->_init()->getOriginalHeight());
        $this->assertEquals(array(272, 261), $this->_init()->getOriginalSizeArray());
    }

    public function testValidateUploadFile()
    {
        $this->assertTrue($this->_helper->validateUploadFile(self::$_fixtureMediaDir . '/m/a/magento_thumbnail.jpg'));
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testValidateUploadFileException()
    {
        $this->_helper->validateUploadFile(__FILE__);
    }

    /**
     * Initialize image by specified type
     *
     * @param string $imageType
     * @return Mage_Catalog_Helper_Image
     */
    protected function _init($imageType = 'image')
    {
        return $this->_helper->init(self::$_product, $imageType);
    }
}
