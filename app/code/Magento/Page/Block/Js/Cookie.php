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

class Cookie extends \Magento\Core\Block\Template
{
    /**
     * @var Zend\Session\Config\ConfigInterface
     */
    protected $sessionConfig;

    /**
     * @param \Zend\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Zend\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->sessionConfig = $sessionConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->sessionConfig->getCookieDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            $domain = '.' . $domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->sessionConfig->getCookiePath();
    }
}
