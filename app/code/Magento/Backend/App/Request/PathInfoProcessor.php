<?php
/**
 * Prevents path info processing for admin store
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Request;

class PathInfoProcessor implements \Magento\App\Request\PathInfoProcessorInterface
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Core\App\Request\PathInfoProcessor
     */
    private $_subject;

    /**
     * @param \Magento\Core\App\Request\PathInfoProcessor $subject
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Magento\Core\App\Request\PathInfoProcessor $subject, \Magento\Backend\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        $this->_subject = $subject;
    }

    /**
     * Process path info
     *
     * @param \Magento\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\App\RequestInterface $request, $pathInfo)
    {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $storeCode = $pathParts[0];

        if ($storeCode != $this->_helper->getAreaFrontName()) {
            return $this->_subject->process($request, $pathInfo);
        }
        return $pathInfo;
    }
}