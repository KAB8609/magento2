<?php
/**
 * Google Optimizer Scripts Block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Block;

abstract class AbstractCode extends \Magento\View\Element\Template
{
    /**
     * @var Entity name in registry
     */
    protected $_registryName;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\GoogleOptimizer\Helper\Code
     */
    protected $_codeHelper;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GoogleOptimizer\Helper\Data $helper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\GoogleOptimizer\Helper\Code $codeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GoogleOptimizer\Helper\Data $helper,
        \Magento\Core\Model\Registry $registry,
        \Magento\GoogleOptimizer\Helper\Code $codeHelper,
        array $data = array()
    ) {
        $this->_helper = $helper;
        $this->_registry = $registry;
        $this->_codeHelper = $codeHelper;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get google experiment code model
     *
     * @return \Magento\GoogleOptimizer\Model\Code
     * @throws \RuntimeException
     */
    protected function _getGoogleExperiment()
    {
        return $this->_codeHelper->getCodeObjectByEntity($this->_getEntity());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . $this->_getScriptCode();
    }

    /**
     * Return script code
     *
     * @return string
     */
    protected function _getScriptCode()
    {
        $result = '';

        if ($this->_helper->isGoogleExperimentActive() && $this->_getGoogleExperiment()) {
            $result = $this->_getGoogleExperiment()->getData('experiment_script');
        }
        return $result;
    }

    /**
     * Get entity from registry
     *
     * @return mixed
     * @throws \RuntimeException
     */
    protected function _getEntity()
    {
        $entity = $this->_registry->registry($this->_registryName);
        if (!$entity) {
            throw new \RuntimeException('Entity is not found in registry.');
        }
        return $entity;
    }
}
