<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Auth session model
 *
 * @category   Mage
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Session extends Magento_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('adminhtml');
    }
}
