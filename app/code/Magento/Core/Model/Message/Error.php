<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Core_Model_Message_Error extends Magento_Core_Model_Message_Abstract
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(Magento_Core_Model_Message::ERROR, $code);
    }
}
