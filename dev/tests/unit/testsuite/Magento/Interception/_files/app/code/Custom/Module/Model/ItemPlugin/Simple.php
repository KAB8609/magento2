<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Custom_Module_Model_ItemPlugin_Simple
{
    /**
     * @param string $invocationResult
     * @return string
     */
    public function afterGetName($invocationResult)
    {
        return $invocationResult . '!';
    }
}