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
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data['simple_advanced_inventory'] = $this->getSimpleAdvancedInventory();
        $this->_data['simple_with_new_category'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildSimpleWithNewCategoryData($defaultData),
        );
        $this->_data['simple_advanced_pricing'] = $this->getSimpleAdvancedPricing();
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
                'category_name' => array(
                    'value' => 'New category %isolation%',
                ),
                'parent_category' => array(
                    'value' => 'Default',
                ),
            ),
            'fields' => array_intersect_key($defaultData['fields'], array_flip(array('name', 'sku', 'price'))),
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
                    'inventory_manage_stock' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                    ),
                    'inventory_qty' => array(
                        'value' => 1,
                        'group' => Fixture\Product::GROUP_PRODUCT_INVENTORY
                    )
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
                    'special_price' => array(
                        'value' => '9',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $pricing);

        return $product;
    }

    protected function resetRequiredFields($productType)
    {
        return array_replace_recursive(
            parent::resetRequiredFields($productType),
            array(
                'data' => array(
                    'fields' => array(
                        'price' => array(
                            'value' => '1.99',
                            'group' => Fixture\Product::GROUP_PRODUCT_DETAILS
                        )
                    )
                )
            )
        );
    }
}
