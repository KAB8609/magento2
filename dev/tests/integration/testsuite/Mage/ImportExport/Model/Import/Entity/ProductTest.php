<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_Product
 */
class Mage_ImportExport_Model_Import_Entity_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Import_Entity_Product
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_ImportExport_Model_Import_Entity_Product();
    }

    public function tearDown()
    {
        unset($this->_model);
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
     * Tests adding of custom options with different behaviours
     *
     * @param $behavior
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @dataProvider getBehaviorDataProvider
     * @covers Mage_ImportExport_Model_Import_Entity_Product::_saveCustomOptions
     */
    public function testSaveCustomOptionsDuplicate($behavior)
    {
        // import data from CSV file
        $pathToFile = __DIR__ . '/_files/product_with_custom_options.csv';
        $source = new Mage_ImportExport_Model_Import_Adapter_Csv($pathToFile);
        $this->_model->setSource($source)
            ->setParameters(array('behavior' => $behavior))
            ->isDataValid();
        $this->_model->importData();

        $product = new Mage_Catalog_Model_Product();
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
                    $valueKey = Mage_ImportExport_Model_Import_Entity_Product_Option::COLUMN_PREFIX
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
     * @param array $expected
     * @param Mage_Catalog_Model_Resource_Product_Option_Collection $options
     * @return array
     */
    protected function _mergeWithExistingData(array $expected,
        Mage_Catalog_Model_Resource_Product_Option_Collection $options
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
     * @param Mage_Catalog_Model_Resource_Product_Option_Collection $options
     * @return array
     */
    protected function _getActualOptionsData(Mage_Catalog_Model_Resource_Product_Option_Collection $options)
    {
        $actualOptionId = 0;
        $actualOptions = array();  // array of type and title types, key is element ID
        $actualData = array();     // array of option data
        $actualValues = array();   // array of option values data
        /** @var $option Mage_Catalog_Model_Product_Option */
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
     * @param Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getOptionData(Mage_Catalog_Model_Product_Option $option)
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
     * @param Mage_Catalog_Model_Product_Option $option
     * @return array|bool
     */
    protected function _getOptionValues(Mage_Catalog_Model_Product_Option $option)
    {
        $values = $option->getValues();
        if (!empty($values)) {
            $result = array();
            /** @var $value Mage_Catalog_Model_Product_Option_Value */
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
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND
            ),
            'Replace behavior' => array(
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE
            )
        );
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
