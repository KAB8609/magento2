<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Js;

class Components extends \Magento\View\Block\Template
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\Block\Template\Context $context,
        \Magento\App\State $appState,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_appState = $appState;
    }

    /**
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->_appState->getMode() == \Magento\App\State::MODE_DEVELOPER;
    }
}
