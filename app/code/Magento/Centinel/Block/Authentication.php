<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel validation frame
 */
namespace Magento\Centinel\Block;

class Authentication extends \Magento\Core\Block\Template
{
    /**
     * Strage for identifiers of related blocks
     *
     * @var array
     */
    protected $_relatedBlocks = array();

    /**
     * Flag - authentication start mode
     * @see self::setAuthenticationStartMode
     *
     * @var bool
     */
    protected $_authenticationStartMode = false;

    /**
     * Add identifier of related block
     *
     * @param string $blockId
     * @return \Magento\Centinel\Block\Authentication
     */
    public function addRelatedBlock($blockId)
    {
        $this->_relatedBlocks[] = $blockId;
        return $this;
    }

    /**
     * Return identifiers of related blocks
     *
     * @return array
     */
    public function getRelatedBlocks()
    {
        return $this->_relatedBlocks;
    }

    /**
     * Check whether authentication is required and prepare some template data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $method = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote()->getPayment()->getMethodInstance();
        if ($method->getIsCentinelValidationEnabled()) {
            $centinel = $method->getCentinelValidator();
            if ($centinel && $centinel->shouldAuthenticate()) {
                $this->setAuthenticationStart(true);
                $this->setFrameUrl($centinel->getAuthenticationStartUrl());
                return parent::_toHtml();
            }
        }
        return parent::_toHtml();
    }
}
