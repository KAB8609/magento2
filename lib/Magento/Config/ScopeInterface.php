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

    /**
     * Retrieve list of all scopes
     *
     * @return array
     */
    public function getAllScopes();

    /**
     * Set current configuration scope
     *
     * @param string $scope
     */
    public function setCurrentScope($scope);
}
