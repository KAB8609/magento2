<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source import behavior model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ImportExport_Model_Source_Import_BehaviorAbstract
{
    /**
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }
    }

    /**
     * Helper getter
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _helper($helperName)
    {
        return isset($this->_helpers[$helperName]) ? $this->_helpers[$helperName] : Mage::helper($helperName);
    }

    /**
     * Get array of possible values
     *
     * @abstract
     * @return array
     */
    abstract public function toArray();

    /**
     * Prepare and return array of option values
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array(array(
            'label' => __('-- Please Select --'),
            'value' => ''
        ));
        $options = $this->toArray();
        if (is_array($options) && count($options) > 0) {
            foreach ($options as $value => $label) {
                $optionArray[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        return $optionArray;
    }

    /**
     * Get current behaviour group code
     *
     * @abstract
     * @return string
     */
    abstract public function getCode();
}
