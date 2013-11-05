<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * FileSystem Interface
 */
namespace Magento\View;

interface PublicFilesManagerInterface
{
    /**
     * Get public file path
     *
     * @param string $filePath
     * @param array $params
     * @return string
     */
    public function getPublicFilePath($filePath, $params);
}
