<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block;

/**
 * Links list block
 */
class Links extends \Magento\View\Element\Template
{
    /** @var string */
    protected $_template = 'Magento_Theme::links.phtml';

    /**
     * @return \Magento\Page\Block\Link[]
     */
    public function getLinks()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    /**
     * Render Block
     *
     * @param \Magento\View\Element\AbstractBlock $link
     * @return string
     */
    public function renderLink(\Magento\View\Element\AbstractBlock $link)
    {
        return $this->_layout->renderElement($link->getNameInLayout());
    }
}
