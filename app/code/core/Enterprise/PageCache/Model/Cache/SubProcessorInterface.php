<?php
/**
 * FPC sub-processor interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Enterprise_PageCache_Model_Cache_SubProcessorInterface
{
    /**
     * Check if request can be cached
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function allowCache(Zend_Controller_Request_Http $request);

    /**
     * Replace block content to placeholder replacer
     *
     * @param string $content
     * @return string
     */
    public function replaceContentToPlaceholderReplacer($content);

    /**
     * Prepare response body before caching
     *
     * @param Zend_Controller_Response_Http $response
     * @return string
     */
    public function prepareContent(Zend_Controller_Response_Http $response);

    /**
     * Return cache page id with application. Depends on GET super global array.
     *
     * @param Enterprise_PageCache_Model_Cache_ProcessorInterface $processor
     * @return string
     */
    public function getPageIdInApp(Enterprise_PageCache_Model_Cache_ProcessorInterface $processor);

    /**
     * Return cache page id without application. Depends on GET super global array.
     *
     * @param Enterprise_PageCache_Model_Cache_ProcessorInterface $processor
     * @return string
     */
    public function getPageIdWithoutApp(Enterprise_PageCache_Model_Cache_ProcessorInterface $processor);
}
