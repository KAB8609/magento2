<?php
/**
 * Application. Performs user requested actions.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

interface AppInterface
{
    /**
     * @return int
     */
    public function execute();
} 