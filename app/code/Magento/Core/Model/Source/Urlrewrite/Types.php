<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UrlRewrite Type source model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Source_Urlrewrite_Types
{
    const SYSTEM = 1;
    const CUSTOM = 0;

    /**
     * @var array|null
     */
    protected $_options = null;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                self::SYSTEM => Mage::helper('Magento_Adminhtml_Helper_Data')->__('System'),
                self::CUSTOM => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Custom')
            );
        }
        return $this->_options;
    }
}
