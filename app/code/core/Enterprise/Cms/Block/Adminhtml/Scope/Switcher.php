<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store switcher block
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Scope_Switcher extends Mage_Adminhtml_Block_System_Config_Switcher
{
    /**
     * Scope switcher options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Get scope switcher options
     *
     * @return array
     */
    public function getStoreSelectOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = parent::getStoreSelectOptions();
            $this->_options['default']['label'] = Mage::helper('Enterprise_Cms_Helper_Data')->__('All Store Views');
        }

        return $this->_options;
    }

    /**
     * Get websites count
     *
     * @return int
     */
    public function isVisible()
    {
        return count($this->getStoreSelectOptions()) > 5;
    }

    /**
     * Get switcher default option value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        foreach ($this->getStoreSelectOptions() as $value => $option) {
            if (array_key_exists('selected', $option) && $option['selected']) {
                return $value;
            }
        }

        return '';
    }
}
