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

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Bundle\Test\Fixture\Bundle as BundleFixture;
use Magento\Catalog\Test\Block\Product\View\Options;

/**
 * Class Bundle
 * Catalog bundle product info block
 *
 * @package Magento\Bundle\Test\Block\Catalog\Product\View\Type
 */
class Bundle extends Options
{
    /**
     * Fill bundle options
     *
     * @param array $bundleOptions
     */
    public function fillBundleOptions($bundleOptions)
    {
        $index = 1;
        foreach ($bundleOptions as $option) {
            $optionClass = '\\Magento\\Bundle\\Test\\Block\\Catalog\\Product\\View\\Type\\Option\\'
                . ucfirst($option['type']);
            $optionBlock = new $optionClass(
                $this->_rootElement->find('.field.option.required:nth-of-type(' . $index++ . ')')
            );
            $optionBlock->fillOption($option);
        }
    }
}