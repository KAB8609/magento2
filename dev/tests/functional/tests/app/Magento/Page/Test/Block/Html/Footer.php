<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Test\Block\Html;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Footer block
 *
 * @package Magento\Page\Test\Block\Html
 */
class Footer extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '//*[@class="links"]//a[contains(text(), "%s")]';

    /**
     * Click on link by name
     *
     * @param string $linkName
     * @return \Mtf\Client\Element
     * @throws \Exception
     */
    public function clickLink($linkName)
    {
        $link = $this->_rootElement->find(sprintf($this->linkSelector, $linkName), Locator::SELECTOR_XPATH);
        if (!$link->isVisible()) {
            throw new \Exception(sprintf('"%s" link is not visible', $linkName));
        }
        $link->click();
    }
}