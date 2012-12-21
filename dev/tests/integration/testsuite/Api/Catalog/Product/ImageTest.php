<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API work with product images
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api_Catalog_Product_ImageTest extends Magento_Test_Webservice
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    protected $_requestData;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $productFixture = require dirname(__FILE__) . '/_fixture/ProductData.php';
        $product        = Mage::getModel('Mage_Catalog_Model_Product');

        $product->setData($productFixture['create_full_fledged']);
        $product->save();

        $this->_product = $product;
        $this->_requestData = array(
            'label'    => 'My Product Image',
            'position' => 2,
            'types'    => array('small_image', 'image', 'thumbnail'),
            'exclude'  => 1,
            'remove'   => 0,
            'file'     => array(
                'name'    => 'my_image_file',
                'content' => null,
                'mime'    => 'image/jpeg'
            )
        );

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        self::callModelDelete($this->_product, true);
        unset($this->_product);
        unset($this->_requestData);

        parent::tearDown();
    }

    /**
     * Tests valid image for product creation
     *
     * @dataProvider validImageProvider
     * @param strign $validImgPath Absolute path to valid image file
     * @return void
     */
    public function testCreateValidImage($validImgPath)
    {
        $product = $this->_product;
        $requestData = $this->_requestData;

        // valid JPG image file
        $requestData['file']['content'] = base64_encode(file_get_contents($validImgPath));

        $imagePath = $this->getWebService()->call(
            'product_attribute_media.create', array('productId' => $product->getSku(), 'data' => $requestData)
        );
        $this->assertInternalType('string', $imagePath, 'String type of response expected but not received');

        // reload product to reflect changes done by API request
        $product->load($product->getId());

        // retrieve saved image
        $attributes  = $product->getTypeInstance()->getSetAttributes($product);
        $imageParams = $attributes['media_gallery']->getBackend()->getImage($product, $imagePath);

        $this->assertInternalType('array', $imageParams, 'Image not found');
        $this->assertEquals($requestData['label'], $imageParams['label'], 'Label does not match');
        $this->assertEquals($requestData['position'], $imageParams['position'], 'Position does not match');
        $this->assertEquals($requestData['exclude'], $imageParams['disabled'], 'Disabled does not match');
    }

    /**
     * Tests not an image for product creation
     *
     * @return void
     */
    public function testCreateNotAnImage()
    {
        $product = $this->_product;
        $requestData = $this->_requestData;

        // TXT file
        $requestData['file']['content'] = base64_encode(
            file_get_contents(dirname(__FILE__) . '/_fixture/_data/files/test.txt')
        );

        try {
            $this->getWebService()->call(
                'product_attribute_media.create', array('productId' => $product->getSku(), 'data' => $requestData)
            );
        } catch (Exception $e) {
            $this->assertEquals('Unsupported image format.', $e->getMessage(), 'Invalid exception message');
        }
        // reload product to reflect changes done by API request
        $product->load($product->getId());

        $mediaData = $product->getData('media_gallery');

        $this->assertCount(0, $mediaData['images'], 'Invalid image file has been saved');
    }

    /**
     * Tests an invalid image for product creation
     *
     * @dataProvider invalidImageProvider
     * @param strign $invalidImgPath Absolute path to invalid image file
     * @return void
     */
    public function testCreateInvalidImage($invalidImgPath)
    {
        $product = $this->_product;
        $requestData = $this->_requestData;

        // Not an image file with JPG extension
        $requestData['file']['content'] = base64_encode(file_get_contents($invalidImgPath));

        try {
            $this->getWebService()->call(
                'product_attribute_media.create', array('productId' => $product->getSku(), 'data' => $requestData)
            );
        } catch (Exception $e) {
            $this->assertEquals('Unsupported image format.', $e->getMessage(), 'Invalid exception message');
        }
        // reload product to reflect changes done by API request
        $product->load($product->getId());

        $mediaData = $product->getData('media_gallery');

        $this->assertCount(0, $mediaData['images'], 'Invalid image file has been saved');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function invalidImageProvider()
    {
        return array(
            array(dirname(__FILE__) . '/_fixture/_data/files/images/test.bmp.jpg'),
            array(dirname(__FILE__) . '/_fixture/_data/files/images/test.php.jpg')
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function validImageProvider()
    {
        return array(
            array(dirname(__FILE__) . '/_fixture/_data/files/images/test.jpg.jpg'),
            array(dirname(__FILE__) . '/_fixture/_data/files/images/test.png.jpg')
        );
    }
}
