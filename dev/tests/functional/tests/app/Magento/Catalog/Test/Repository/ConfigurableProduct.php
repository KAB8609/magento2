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

use Mtf\Repository\AbstractRepository;
use Magento\Catalog\Test\Fixture\ConfigurableProduct as ConfigurableProductFixture;

/**
 * Class Configurable Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class ConfigurableProduct extends AbstractRepository
{
    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['configurable_required'] = $this->_data['default'];
        $this->_data['configurable'] = $this->_data['default'];
        $this->_data['configurable']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['configurable']['data']['affect_configurable_product_attributes'] = 'Template %isolation%';

        $this->_data['configurable_advanced_pricing'] = $this->getConfigurableAdvancedPricing();

        $this->_data['product_variations'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildProductVariations($defaultData),
        );
    }

    /**
     * Get configurable product with advanced pricing
     *
     * @return array
     */
    protected function getConfigurableAdvancedPricing()
    {
        $pricing = array(
            'data' => array(
                'fields' => array(
                    'special_price' => array(
                        'value' => '9',
                        'group' => 'product_info_tabs_advanced-pricing'
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['configurable'], $pricing);

        return $product;
    }

    /**
     * Build product variations data set
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildProductVariations(array $defaultData)
    {
        $data = $defaultData;
        $data['affect_configurable_product_attributes'] = 'Template %isolation%';
        $data['fields'] = array(
            'configurable_attributes_data' => array(
                'value' => array(
                    '0' => array(
                        'label' => array(
                            'value' => '%new_attribute_label%'
                        ),
                        '0' => array(
                            'option_label' => array(
                                'value' => '%new_attribute_option_1_label%',
                            ),
                            'include' => array(
                                'value' => 'Yes',
                            ),
                        ),
                        '1' => array(
                            'option_label' => array(
                                'value' => '%new_attribute_option_2_label%',
                            ),
                            'include' => array(
                                'value' => 'Yes',
                            ),
                        ),
                    ),
                ),
                'group' => ConfigurableProductFixture::GROUP_VARIATIONS,
            ),
            'variations-matrix' => array(
                'value' => array(
                    '0' => array(
                        'configurable_attribute' => array(
                            '0' => array(
                                'attribute_option' => '%new_attribute_option_1_label%',
                            ),
                        ),
                        'value' => array(
                            'qty' => array(
                                'value' => 100,
                            ),
                        ),
                    ),
                    '1' => array(
                        'configurable_attribute' => array(
                            '0' => array(
                                'attribute_option' => '%new_attribute_option_2_label%',
                            ),
                        ),
                        'value' => array(
                            'qty' => array(
                                'value' => 100,
                            ),
                        ),
                    ),
                ),
                'group' => ConfigurableProductFixture::GROUP_VARIATIONS,
            ),
        );
        return $data;
    }
}
