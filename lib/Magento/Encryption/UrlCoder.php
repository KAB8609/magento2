<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Encryption;

class UrlCoder
{
    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\UrlInterface $url
     */
    public function __construct(\Magento\UrlInterface $url)
    {
        $this->_url = $url;
    }

    /**
     * base64_encode() for URLs encoding
     *
     * @param    string $url
     * @return   string
     */
    public function encode($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }

    /**
     *  base64_decode() for URLs decoding
     *
     * @param    string $url
     * @return   string
     */
    public function decode($url)
    {
        return $this->_url->sessionUrlVar(base64_decode(strtr($url, '-_,', '+/=')));
    }
} 