<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core translate helper
 */
namespace Magento\Core\Helper;

class Translate extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Design package instance
     *
     * @var \Magento\View\Design
     */
    protected $_design;
    
    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\View\Design $design
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\View\Design $design,
        \Magento\Core\Model\Event\Manager $eventManager
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context);
    }

    /**
     * Save translation data to database for specific area
     *
     * @param array $translate
     * @param string $area
     * @param string $returnType
     * @return string
     */
    public function apply($translate, $area, $returnType = 'json')
    {
        try {
            if ($area) {
                $this->_design->setArea($area);
            }

            $this->_translator->processAjaxPost($translate);
            $result = $returnType == 'json' ? "{success:true}" : true;
        } catch (\Exception $e) {
            $result = $returnType == 'json' ? "{error:true,message:'" . $e->getMessage() . "'}" : false;
        }
        return $result;
    }

    /**
     * This method initializes the Translate object for this instance.
     * @param $localeCode string
     * @param $area string
     * @param $forceReload bool
     * @return \Magento\Core\Model\Translate
     */
    public function initTranslate($localeCode, $area, $forceReload)
    {
        $this->_translator->setLocale($localeCode);

        $dispatchResult = new \Magento\Object(array(
            'inline_type' => null,
            'params' => array('area' => $area)
        ));
        $this->_eventManager->dispatch('translate_initialization_before', array(
            'translate_object' => $this->_translator,
            'result' => $dispatchResult
        ));
        $this->_translator->init($area, $dispatchResult, $forceReload);
        return $this;
    }
}
