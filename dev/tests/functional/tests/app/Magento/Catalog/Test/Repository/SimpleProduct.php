<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Test\Repository;

use Magento\Catalog\Test\Fixture;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class SimpleProduct extends Product
{
    const ADVANCED_INVENTORY = 'simple_advanced_inventory';

    const ADVANCED_PRICING = 'simple_advanced_pricing';

    const SIMPLE_WITH_MAP = 'simple_with_map';

    const SIMPLE_OUT_OF_STOCK = 'simple_out_of_stock';

    const BASE = 'simple';

    const CUSTOM_OPTIONS = 'simple_custom_options';

    const NEW_CATEGORY = 'simple_with_new_category';

    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data[self::ADVANCED_INVENTORY] = $this->getSimpleAdvancedInventory();
        $this->_data[self::NEW_CATEGORY] = array(
            'config' => $defaultConfig,
            'data' => $this->buildSimpleWithNewCategoryData($defaultData)
        );
        $this->_data[self::ADVANCED_PRICING] = $this->getSimpleAdvancedPricing();
        $this->_data[self::CUSTOM_OPTIONS] = $this->getSimpleCustomOption();
        $this->_data[self::SIMPLE_WITH_MAP] = $this->getSimpleAppliedMap($defaultData);
        $this->_data[self::SIMPLE_OUT_OF_STOCK] = $this->_getSimpleOutOfStock();
    }

    /**
     * Build data for simple product with new category
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildSimpleWithNewCategoryData($defaultData)
    {
        return array(
            'category_new' => array(
                'category_name' => array('value' => 'New category %isolation%'),
                'parent_category' => array('value' => 'Default')
            ),
            'category_name' => '%category::getCategoryName%',
            'category_id' => '%category::getCategoryId%',
            'fields' => array_intersect_key(
                $defaultData['fields'],
                array_flip(array('name', 'sku', 'price', 'weight'))
            )
        );
    }

    /**
     * Get simple product with advanced inventory
     *
     * @return array
     */
    protected function getSimpleAdvancedInventory()
    {
        $inventory = array(
            'data' => array(
                'fields' => array(
                    'inventory_manage_stock' => array('value' => 'Yes', 'input_value' => '1'),
                    'inventory_qty' => array('value' => 1, 'group' => Fixture\Product::GROUP_PRODUCT_INVENTORY)
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $inventory);
        unset($product['data']['fields']['qty']);

        return $product;
    }

    /**
     * Get simple product with advanced pricing
     *
     * @return array
     */
    protected function getSimpleAdvancedPricing()
    {
        $pricing = array(
            'data' => array(
                'fields' => array(
                    'special_price' => array('value' => '9', 'group' => Fixture\Product::GROUP_PRODUCT_PRICING)
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $pricing);

        return $product;
    }

    /**
     * @param string $productType
     * @return array
     */
    protected function resetRequiredFields($productType)
    {
        return array_replace_recursive(
            parent::resetRequiredFields($productType),
            array(
                'data' => array(
                    'fields' => array(
                        'price' => array('value' => '1.99', 'group' => Fixture\Product::GROUP_PRODUCT_DETAILS)
                    )
                )
            )
        );
    }

    /**
     * @return array
     */
    protected function getSimpleCustomOption()
    {
        return array_merge_recursive(
            $this->_data['simple'],
            array(
                'data' => array(
                    'fields' => array(
                        'custom_options' => array(
                            'value' => array(
                                array(
                                    'title' => 'custom option drop down',
                                    'is_require' => true,
                                    'type' => 'Drop-down',
                                    'options' => array(
                                        array(
                                            'title' => 'Title Drop - down 1',
                                            'price' => 2.56,
                                            'price_type' => 'Fixed',
                                            'sku' => 'sku_drop_down_row_1'
                                        )
                                    )
                                )
                            ),
                            'group' => Fixture\Product::GROUP_CUSTOM_OPTIONS
                        )
                    )
                )
            )
        );
    }

    /**
     * Get simple product with advanced pricing (MAP)
     *
     * @return array
     */
    protected function getSimpleAppliedMap()
    {
        $pricing = array(
            'data' => array(
                'fields' => array(
                    'msrp_enabled' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING,
                        'input' => 'select'
                    ),
                    'msrp_display_actual_price_type' => array(
                        'value' => 'On Gesture',
                        'input_value' => '1',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING,
                        'input' => 'select'
                    ),
                    'msrp' => array(
                        'value' => '15',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $pricing);

        return $product;
    }
}
