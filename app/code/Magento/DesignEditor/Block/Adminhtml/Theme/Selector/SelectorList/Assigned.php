<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Assigned theme list
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList;

class Assigned
    extends \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\AbstractSelectorList
{
    /**
     * Store manager model
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get list title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Themes Assigned to Store Views');
    }

    /**
     * Add theme buttons
     *
     * @param \Magento\DesignEditor\Block\Adminhtml\Theme $themeBlock
     * @return \Magento\DesignEditor\Block\Adminhtml\Theme\Selector\SelectorList\Assigned
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);
        $this->_addDuplicateButtonHtml($themeBlock);
        if (count($this->_storeManager->getStores()) > 1) {
            $this->_addAssignButtonHtml($themeBlock);
        }
        $this->_addEditButtonHtml($themeBlock);
        return $this;
    }
}
