<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Config;

interface ScopeInterface
{
    /**
     * Get current configuration scope identifier
     *
     * @return string
     */
    public function getCurrentScope();
}
