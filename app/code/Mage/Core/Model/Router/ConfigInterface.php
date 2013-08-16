<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Router_ConfigInterface
{

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $areaCode
     * @param string $routerId
     * @return array
     */
    public function getRoutes($areaCode, $routerId);
}