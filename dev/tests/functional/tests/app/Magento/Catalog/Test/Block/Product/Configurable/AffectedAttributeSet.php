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

namespace Magento\Catalog\Test\Block\Product\Configurable;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Class AffectedAttributeSet
 * Choose affected attribute set dialog popup window
 *
 * @package Magento\Catalog\Test\Block\Product\Configurable
 */
class AffectedAttributeSet extends Block
{
    /**
     * Create new attribute set based on default
     *
     * @var string
     */
    private $affectedAttributeSet;

    /**
     * New attribute set name
     *
     * @var string
     */
    private $attributeSetName;

    /**
     * 'Confirm' button
     *
     * @var string
     */
    private $confirmButton;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        //Elements
        $this->affectedAttributeSet = '[name=affected-attribute-set][value=new]';
        $this->attributeSetName = '[name=new-attribute-set-name]';
        $this->confirmButton = '[id*=confirm-button]';
    }

    /**
     * Choose affected attribute set
     *
     * @param ConfigurableProduct $fixture
     */
    public function chooseAttributeSet(ConfigurableProduct $fixture)
    {
        $attributeSetName = $fixture->getAffectedAttributeSet();
        if ($attributeSetName) {
            $this->_rootElement->find($this->affectedAttributeSet, Locator::SELECTOR_CSS)->click();
            $this->_rootElement->find($this->attributeSetName, Locator::SELECTOR_CSS)->setValue($attributeSetName);
        }
        $this->_rootElement->find($this->confirmButton, Locator::SELECTOR_CSS)->click();
    }
}