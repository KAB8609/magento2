<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for showing product options fieldsets
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author    Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Composite;

class Fieldset extends \Magento\View\Element\Text\ListText
{
    /**
     *
     * Iterates through fieldsets and fetches complete html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $children = $this->getLayout()->getChildBlocks($this->getNameInLayout());
        $total = count($children);
        $i = 0;
        $this->setText('');
        /** @var $block \Magento\View\Element\AbstractBlock  */
        foreach ($children as $block) {
            $i++;
            $block->setIsLastFieldset($i == $total);

            $this->addText($block->toHtml());
        }

        return parent::_toHtml();
    }
}
