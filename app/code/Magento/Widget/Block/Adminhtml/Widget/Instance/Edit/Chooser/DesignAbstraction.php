<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

/**
 * Widget Instance design abstractions chooser
 *
 * @method getArea()
 * @method getTheme()
 */
class DesignAbstraction extends \Magento\View\Block\Html\Select
{
    /**
     * @var \Magento\View\Layout\ProcessorFactory
     */
    protected $_layoutProcessorFactory;

    /**
     * @var \Magento\Core\Model\Resource\Theme\CollectionFactory
     */
    protected $_themesFactory;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\View\Block\Context $context
     * @param \Magento\View\Layout\ProcessorFactory $layoutProcessorFactory
     * @param \Magento\Core\Model\Resource\Theme\CollectionFactory $themesFactory
     * @param \Magento\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Context $context,
        \Magento\View\Layout\ProcessorFactory $layoutProcessorFactory,
        \Magento\Core\Model\Resource\Theme\CollectionFactory $themesFactory,
        \Magento\App\State $appState,
        array $data = array()
    ) {
        $this->_layoutProcessorFactory = $layoutProcessorFactory;
        $this->_themesFactory = $themesFactory;
        $this->_appState = $appState;
        parent::__construct($context, $data);
    }

    /**
     * Add necessary options
     *
     * @return \Magento\View\Block\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('', __('-- Please Select --'));
            $layoutUpdateParams = array(
                'theme' => $this->_getThemeInstance($this->getTheme()),
            );
            $designAbstractions = $this->_appState->emulateAreaCode(
                'frontend',
                array($this->_getLayoutProcessor($layoutUpdateParams), 'getAllDesignAbstractions')
            );
            $this->_addDesignAbstractionOptions($designAbstractions);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve theme instance by its identifier
     *
     * @param int $themeId
     * @return \Magento\Core\Model\Theme|null
     */
    protected function _getThemeInstance($themeId)
    {
        /** @var \Magento\Core\Model\Resource\Theme\Collection $themeCollection */
        $themeCollection = $this->_themesFactory->create();
        return $themeCollection->getItemById($themeId);
    }

    /**
     * Retrieve new layout merge model instance
     *
     * @param array $arguments
     * @return \Magento\View\Layout\ProcessorInterface
     */
    protected function _getLayoutProcessor(array $arguments)
    {
        return $this->_layoutProcessorFactory->create($arguments);
    }

    /**
     * Add design abstractions information to the options
     *
     * @param array $designAbstractions
     */
    protected function _addDesignAbstractionOptions(array $designAbstractions)
    {
        $label = array();
        // Sort list of design abstractions by label
        foreach ($designAbstractions as $key => $row) {
            $label[$key]  = $row['label'];
        }
        array_multisort($label, SORT_STRING, $designAbstractions);

        // Group the layout options
        $customLayouts = array();
        $pageLayouts = array();

        foreach ($designAbstractions as $pageTypeName => $pageTypeInfo) {
            if ($pageTypeInfo['design_abstraction'] ===
                \Magento\Core\Model\Layout\Merge::DESIGN_ABSTRACTION_PAGE_LAYOUT) {
                    $pageLayouts[] = array('value' => $pageTypeName, 'label' => $pageTypeInfo['label']);
            }
            else {
                $customLayouts[] = array('value' => $pageTypeName, 'label' => $pageTypeInfo['label']);
            }
        }
        $params = array();
        $this->addOption($customLayouts, __('Custom Layouts'), $params);
        $this->addOption($pageLayouts, __('Page Layouts'), $params);
    }
}