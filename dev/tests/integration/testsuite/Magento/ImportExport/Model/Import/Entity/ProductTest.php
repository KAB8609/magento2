<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\Entity\Product
 *
 * The "CouplingBetweenObjects" warning is caused by tremendous complexity of the original class
 *
 */
namespace Magento\ImportExport\Model\Import\Entity;

/**
 * Class ProductTest
 * @package Magento\ImportExport\Model\Import\Entity
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Import\Entity\Product
     */
    protected $_model;

    /**
     * @var \Magento\ImportExport\Model\Import\Uploader
     */
    protected $_uploader;

    /**
     * @var \Magento\ImportExport\Model\Import\UploaderFactory
     */
    protected $_uploaderFactory;

    protected function setUp()
    {
        $this->_uploaderFactory = $this->getMock('Magento\ImportExport\Model\Import\UploaderFactory',
            array('create'), array(), '', false);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\ImportExport\Model\Import\Entity\Product', array(
                'uploaderFactory' => $this->_uploaderFactory
            ));
    }

    /**
     * Options for assertion
     *
     * @var array
     */
    protected $_assertOptions = array(
        'is_require' => '_custom_option_is_required',
        'price'      => '_custom_option_price',
        'sku'        => '_custom_option_sku',
        'sort_order' => '_custom_option_sort_order',
    );

    /**
     * Option values for assertion
     *
     * @var array
     */
    protected $_assertOptionValues = array('title', 'price', 'sku');

    /**
     * Test if visibility properly saved after import
     *
     * magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testSaveProductsVisibility()
    {
        $existingProductIds = array(10, 11, 12);
        $productsBeforeImport = array();
        foreach ($existingProductIds as $productId) {
            $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
            $product->load($productId);
            $productsBeforeImport[] = $product;
        }

        $source = new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/_files/products_to_import.csv');
        $this->_model->setParameters(array(
            'behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE,
            'entity' => 'catalog_product'
        ))->setSource($source)->isDataValid();

        $this->_model->importData();

        /** @var $productBeforeImport \Magento\Catalog\Model\Product */
        foreach ($productsBeforeImport as $productBeforeImport) {
            /** @var $productAfterImport \Magento\Catalog\Model\Product */
            $productAfterImport = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
            $productAfterImport->load($productBeforeImport->getId());

            $this->assertEquals(
                $productBeforeImport->getVisibility(),
                $productAfterImport->getVisibility()
            );
            unset($productAfterImport);
        }

        unset($productsBeforeImport, $product);
    }

    /**
     * Test if stock item quantity properly saved after import
     *
     * magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testSaveStockItemQty()
    {
        $existingProductIds = array(10, 11, 12);
        $stockItems = array();
        foreach ($existingProductIds as $productId) {
            $stockItem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogInventory\Model\Stock\Item');
            $stockItem->loadByProduct($productId);
            $stockItems[$productId] = $stockItem;
        }

        $source = new \Magento\ImportExport\Model\Import\Source\Csv(__DIR__ . '/_files/products_to_import.csv');
        $this->_model->setParameters(array(
            'behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE,
            'entity' => 'catalog_product'
        ))->setSource($source)->isDataValid();

        $this->_model->importData();

        /** @var $stockItmBeforeImport \Magento\CatalogInventory\Model\Stock\Item */
        foreach ($stockItems as $productId => $stockItmBeforeImport) {

            /** @var $stockItemAfterImport \Magento\CatalogInventory\Model\Stock\Item */
            $stockItemAfterImport = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\CatalogInventory\Model\Stock\Item');
            $stockItemAfterImport->loadByProduct($productId);

            $this->assertEquals(
                $stockItmBeforeImport->getQty(),
                $stockItemAfterImport->getQty()
            );
            unset($stockItemAfterImport);
        }

        unset($stockItems, $stockItem);
    }

    /**
     * Tests adding of custom options with different behaviours
     *
     * @param $behavior
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @dataProvider getBehaviorDataProvider
     */
    public function testSaveCustomOptionsDuplicate($behavior)
    {
        // import data from CSV file
        $pathToFile = __DIR__ . '/_files/product_with_custom_options.csv';
        $source = new \Magento\ImportExport\Model\Import\Source\Csv($pathToFile);
        $this->_model->setSource($source)
            ->setParameters(array('behavior' => $behavior))
            ->isDataValid();
        $this->_model->importData();

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1); // product from fixture
        $options = $product->getProductOptionsCollection();

        $expectedData = $this->_getExpectedOptionsData($pathToFile);
        $expectedData = $this->_mergeWithExistingData($expectedData, $options);
        $actualData = $this->_getActualOptionsData($options);

        // assert of equal type+titles
        $expectedOptions = $expectedData['options']; // we need to save key values
        $actualOptions = $actualData['options'];
        sort($expectedOptions);
        sort($actualOptions);
        $this->assertEquals($expectedOptions, $actualOptions);

        // assert of options data
        $this->assertCount(count($expectedData['data']), $actualData['data']);
        $this->assertCount(count($expectedData['values']), $actualData['values']);
        foreach ($expectedData['options'] as $expectedId => $expectedOption) {
            $elementExist = false;
            // find value in actual options and values
            foreach ($actualData['options'] as $actualId => $actualOption) {
                if ($actualOption == $expectedOption) {
                    $elementExist = true;
                    $this->assertEquals($expectedData['data'][$expectedId], $actualData['data'][$actualId]);
                    if (array_key_exists($expectedId, $expectedData['values'])) {
                        $this->assertEquals($expectedData['values'][$expectedId], $actualData['values'][$actualId]);
                    }
                    unset($actualData['options'][$actualId]); // remove value in case of duplicating key values
                    break;
                }
            }
            $this->assertTrue($elementExist, 'Element must exist.');
        }
    }

    /**
     * Test if datetime properly saved after import
     *
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testSaveDatetimeAttribute()
    {
        $existingProductIds = array(10, 11, 12);
        $productsBeforeImport = array();
        foreach ($existingProductIds as $productId) {
            $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Product');
            $product->load($productId);
            $productsBeforeImport[$product->getSku()] = $product;
        }

        $source = new \Magento\ImportExport\Model\Import\Source\Csv(
            __DIR__ . '/_files/products_to_import_with_datetime.csv'
        );
        $this->_model->setParameters(array(
            'behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE,
            'entity' => 'catalog_product'
        ))->setSource($source)->isDataValid();

        $this->_model->importData();

        reset($source);
        foreach ($source as $row) {
            /** @var $productAfterImport \Magento\Catalog\Model\Product */
            $productBeforeImport = $productsBeforeImport[$row['sku']];

            /** @var $productAfterImport \Magento\Catalog\Model\Product */
            $productAfterImport = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->create('Magento\Catalog\Model\Product');
            $productAfterImport->load($productBeforeImport->getId());
            $this->assertEquals(
                @strtotime($row['news_from_date']),
                @strtotime($productAfterImport->getNewsFromDate())
            );
            unset($productAfterImport);
        }
        unset($productsBeforeImport, $product);
    }

    /**
     * Returns expected product data: current id, options, options data and option values
     *
     * @param string $pathToFile
     * @return array
     */
    protected function _getExpectedOptionsData($pathToFile)
    {
        $productData = $this->_csvToArray(file_get_contents($pathToFile));
        $expectedOptionId = 0;
        $expectedOptions = array();  // array of type and title types, key is element ID
        $expectedData = array();     // array of option data
        $expectedValues = array();   // array of option values data
        foreach ($productData['data'] as $data) {
            if (!empty($data['_custom_option_type']) && !empty($data['_custom_option_title'])) {
                $lastOptionKey = $data['_custom_option_type'] . '|' . $data['_custom_option_title'];
                $expectedOptionId++;
                $expectedOptions[$expectedOptionId] = $lastOptionKey;
                $expectedData[$expectedOptionId] = array();
                foreach ($this->_assertOptions as $assertKey => $assertFieldName) {
                    if (array_key_exists($assertFieldName, $data)) {
                        $expectedData[$expectedOptionId][$assertKey] = $data[$assertFieldName];
                    }
                }
            }
            if (!empty($data['_custom_option_row_title']) && empty($data['_custom_option_store'])) {
                $optionData = array();
                foreach ($this->_assertOptionValues as $assertKey) {
                    $valueKey = \Magento\ImportExport\Model\Import\Entity\Product\Option::COLUMN_PREFIX
                        . 'row_' . $assertKey;
                    $optionData[$assertKey] = $data[$valueKey];
                }
                $expectedValues[$expectedOptionId][] = $optionData;
            }
        }

        return array(
            'id'      => $expectedOptionId,
            'options' => $expectedOptions,
            'data'    => $expectedData,
            'values'  => $expectedValues,
        );
    }

    /**
     * Updates expected options data array with existing unique options data
     *
     * @param array $expected
     * @param \Magento\Catalog\Model\Resource\Product\Option\Collection $options
     * @return array
     */
    protected function _mergeWithExistingData(array $expected,
        \Magento\Catalog\Model\Resource\Product\Option\Collection $options
    ) {
        $expectedOptionId = $expected['id'];
        $expectedOptions = $expected['options'];
        $expectedData = $expected['data'];
        $expectedValues = $expected['values'];
        foreach ($options->getItems() as $option) {
            $optionKey = $option->getType() . '|' . $option->getTitle();
            if (!in_array($optionKey, $expectedOptions)) {
                $expectedOptionId++;
                $expectedOptions[$expectedOptionId] = $optionKey;
                $expectedData[$expectedOptionId] = $this->_getOptionData($option);
                if ($optionValues = $this->_getOptionValues($option)) {
                    $expectedValues[$expectedOptionId] = $optionValues;
                }
            }
        }

        return array(
            'id'      => $expectedOptionId,
            'options' => $expectedOptions,
            'data'    => $expectedData,
            'values'  => $expectedValues,
        );
    }

    /**
     *  Returns actual product data: current id, options, options data and option values
     *
     * @param \Magento\Catalog\Model\Resource\Product\Option\Collection $options
     * @return array
     */
    protected function _getActualOptionsData(\Magento\Catalog\Model\Resource\Product\Option\Collection $options)
    {
        $actualOptionId = 0;
        $actualOptions = array();  // array of type and title types, key is element ID
        $actualData = array();     // array of option data
        $actualValues = array();   // array of option values data
        /** @var $option \Magento\Catalog\Model\Product\Option */
        foreach ($options->getItems() as $option) {
            $lastOptionKey = $option->getType() . '|' . $option->getTitle();
            $actualOptionId++;
            $actualOptions[$actualOptionId] = $lastOptionKey;
            $actualData[$actualOptionId] = $this->_getOptionData($option);
            if ($optionValues = $this->_getOptionValues($option)) {
                $actualValues[$actualOptionId] = $optionValues;
            }
        }
        return array(
            'id'      => $actualOptionId,
            'options' => $actualOptions,
            'data'    => $actualData,
            'values'  => $actualValues,
        );
    }

    /**
     * Retrieve option data
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function _getOptionData(\Magento\Catalog\Model\Product\Option $option)
    {
        $result = array();
        foreach (array_keys($this->_assertOptions) as $assertKey) {
            $result[$assertKey] = $option->getData($assertKey);
        }
        return $result;
    }

    /**
     * Retrieve option values or false for options which has no values
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array|bool
     */
    protected function _getOptionValues(\Magento\Catalog\Model\Product\Option $option)
    {
        $values = $option->getValues();
        if (!empty($values)) {
            $result = array();
            /** @var $value \Magento\Catalog\Model\Product\Option\Value */
            foreach ($values as $value) {
                $optionData = array();
                foreach ($this->_assertOptionValues as $assertKey) {
                    if ($value->hasData($assertKey)) {
                        $optionData[$assertKey] = $value->getData($assertKey);
                    }
                }
                $result[] = $optionData;
            }
            return $result;
        }

        return false;
    }

    /**
     * Data provider for test 'testSaveCustomOptionsDuplicate'
     *
     * @return array
     */
    public function getBehaviorDataProvider()
    {
        return array(
            'Append behavior' => array(
                '$behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND
            ),
            'Replace behavior' => array(
                '$behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE
            )
        );
    }

    /**
     * @magentoDataIsolation enabled
     * @magentoDataFixture mediaImportImageFixture
     */
    public function testSaveMediaImage()
    {
        $attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Entity\Attribute');
        $attribute->loadByCode('catalog_product', 'media_gallery');
        $data = implode(',', array(
            // minimum required set of attributes + media images
            'sku', '_attribute_set', '_type', '_product_websites', 'name', 'price',
            'description', 'short_description', 'weight', 'status', 'visibility', 'tax_class_id',
            '_media_attribute_id', '_media_image', '_media_label', '_media_position', '_media_is_disabled'
        )) . "\n";
        $data .= implode(',', array(
            'test_sku', 'Default', \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE, 'base', 'Product Name', '9.99',
            'Product description', 'Short desc.', '1',
            \Magento\Catalog\Model\Product\Status::STATUS_ENABLED,
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH, 0,
            $attribute->getId(), 'magento_image.jpg', 'Image Label', '1', '0'
        )) . "\n";
        $data = 'data://text/plain;base64,' . base64_encode($data);
        $fixture = new \Magento\ImportExport\Model\Import\Source\Csv($data);

        foreach (\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Product\Collection') as $product) {
            $this->fail("Unexpected precondition - product exists: '{$product->getId()}'.");
        }

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $uploader = $this->getMock('Magento\ImportExport\Model\Import\Uploader',
            array('init'), array(
                $objectManager->create('Magento\Core\Helper\File\Storage\Database'),
                $objectManager->create('Magento\Core\Helper\File\Storage'),
                $objectManager->create('Magento\Image\AdapterFactory'),
                $objectManager->create('Magento\Core\Model\File\Validator\NotProtectedExtension'),
            ));
        $this->_uploaderFactory->expects($this->any())->method('create')->will($this->returnValue($uploader));

        $this->_model->setSource($fixture)
            ->setParameters(array('behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND))
            ->isDataValid();
        $this->_model->importData();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $resource = $objectManager->get('Magento\Catalog\Model\Resource\Product');
        $productId = $resource->getIdBySku('test_sku'); // fixture
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load($productId);
        $gallery = $product->getMediaGalleryImages();
        $this->assertInstanceOf('Magento\Data\Collection', $gallery);
        $items = $gallery->getItems();
        $this->assertCount(1, $items);
        $item = array_pop($items);
        $this->assertInstanceOf('Magento\Object', $item);
        $this->assertEquals('magento_image.jpg', $item->getFile());
        $this->assertEquals('Image Label', $item->getLabel());
    }

    /**
     * Copy a fixture image into media import directory
     */
    public static function mediaImportImageFixture()
    {
        /** @var \Magento\Filesystem\Directory\Write $mediaDirectory */
        $mediaDirectory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Filesystem')->getDirectoryWrite(\Magento\Filesystem::MEDIA);
        $dir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem')
            ->getPath('media') . '/import';
        $mediaDirectory->create('import');
        $dirPath = $mediaDirectory->getAbsolutePath('import');
        copy(__DIR__ . '/../../../../../Magento/Catalog/_files/magento_image.jpg', "{$dirPath}/magento_image.jpg");
    }

    /**
     * Cleanup media import and catalog directories
     */
    public static function mediaImportImageFixtureRollback()
    {
        $media = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')
            ->getDir('media');
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem');
        $filesystem->delete("{$media}/import");
        $filesystem->delete("{$media}/catalog");
    }

    /**
     * Export CSV string to array
     *
     * @param string $content
     * @param mixed $entityId
     * @return array
     */
    protected function _csvToArray($content, $entityId = null)
    {
        $data = array(
            'header' => array(),
            'data'   => array()
        );

        $lines = str_getcsv($content, "\n");
        foreach ($lines as $index => $line) {
            if ($index == 0) {
                $data['header'] = str_getcsv($line);
            } else {
                $row = array_combine($data['header'], str_getcsv($line));
                if (!is_null($entityId) && !empty($row[$entityId])) {
                    $data['data'][$row[$entityId]] = $row;
                } else {
                    $data['data'][] = $row;
                }
            }
        }
        return $data;
    }
}
