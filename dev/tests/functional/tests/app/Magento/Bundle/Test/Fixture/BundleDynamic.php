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

namespace Magento\Bundle\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;

class BundleDynamic extends Bundle
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        parent::_initData();

        $this->_data['fields'] = array(
            'sku_type' => array(
                'value' => 'Dynamic',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'price_type' => array(
                'value' => 'Dynamic',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'weight_type' => array(
                'value' => 'Dynamic',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'shipment_type' => array(
                'value' => 'Separately',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'bundle_selections' => array(
                'value' => array(
                    'bundle_item_0' => array(
                        'title' => array(
                            'value' => 'Drop-down Option'
                        ),
                        'type' => array(
                            'value' => 'Drop-down',
                            'input_value' => 'select'
                        ),
                        'required' => array(
                            'value' => 'Yes',
                            'input_value' => '1'
                        ),
                        'assigned_products' => array(
                            'assigned_product_0' => array(
                                'search_data' => array(
                                    'name' => '%item1_product1::getProductName%',
                                ),
                                'data' => array(
                                    'selection_qty' => array(
                                        'value' => 1
                                    ),
                                    'product_id' => array(
                                        'value' => '%item1_product1::getProductId%'
                                    )
                                )
                            ),
                            'assigned_product_1' => array(
                                'search_data' => array(
                                    'name' => '%item1_product2::getProductName%',
                                ),
                                'data' => array(
                                    'selection_qty' => array(
                                        'value' => 1
                                    ),
                                    'product_id' => array(
                                        'value' => '%item1_product2::getProductId%'
                                    )
                                )
                            )
                        )
                    )
                ),
                'group' => static::GROUP
            )
        ) + $this->_data['fields'];

        $this->_data['checkout'] = array(
            'prices' => '10',
            'selection' => array(
                'bundle_item_0' => 'assigned_product_0'
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
