<?php
/**
 * Abstract Google Experiment Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block\Adminhtml;

abstract class AbstractTab
    extends \Magento\Backend\Block\Widget\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Code
     */
    protected $_codeHelper;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Form
     */
    protected $_formHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GoogleOptimizer\Helper\Data $helperData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\GoogleOptimizer\Helper\Code $codeHelper
     * @param \Magento\GoogleOptimizer\Helper\Form $formHelper
     * @param \Magento\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GoogleOptimizer\Helper\Data $helperData,
        \Magento\Core\Model\Registry $registry,
        \Magento\GoogleOptimizer\Helper\Code $codeHelper,
        \Magento\GoogleOptimizer\Helper\Form $formHelper,
        \Magento\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);

        $this->_helperData = $helperData;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
        $this->_formHelper = $formHelper;
        $this->setForm($formFactory->create());
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $this->_formHelper->addGoogleoptimizerFields($this->getForm(), $this->_getGoogleExperiment());
        return parent::_prepareForm();
    }

    /**
     * Get google experiment code model
     *
     * @return \Magento\GoogleOptimizer\Model\Code|null
     */
    protected function _getGoogleExperiment()
    {
        $entity = $this->_getEntity();
        if ($entity->getId()) {
            return $this->_codeHelper->getCodeObjectByEntity($entity);
        }
        return null;
    }

    /**
     * Get Entity model
     *
     * @return \Magento\Catalog\Model\AbstractModel
     */
    protected abstract function _getEntity();

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_helperData->isGoogleExperimentActive();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}