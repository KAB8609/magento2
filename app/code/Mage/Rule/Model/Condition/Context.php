<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block context object. Will be used as rule condition constructor modification point after release.
 * Important: Should not be modified by extension developers.
 */
class Mage_Rule_Model_Condition_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Mage_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @param Mage_Core_Model_View_Url $viewUrl
     */
    public function __construct(Mage_Core_Model_View_Url $viewUrl)
    {
        $this->_viewUrl = $viewUrl;
    }

    /**
     * @return Mage_Core_Model_View_Url
     */
    public function getViewUrl()
    {
        return $this->_viewUrl;
    }
}