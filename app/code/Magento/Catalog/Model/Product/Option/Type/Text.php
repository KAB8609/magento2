<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option text type
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Option\Type;

class Text extends \Magento\Catalog\Model\Product\Option\Type\DefaultType
{
    /**
     * Validate user input for option
     *
     * @throws \Magento\Core\Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = trim($this->getUserValue());

        // Check requires option to have some value
        if (strlen($value) == 0 && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            \Mage::throwException(__('Please specify the product\'s required option(s).'));
        }

        // Check maximal length limit
        $maxCharacters = $option->getMaxCharacters();
        if ($maxCharacters > 0 && \Mage::helper('Magento\Core\Helper\String')->strlen($value) > $maxCharacters) {
            $this->setIsValid(false);
            \Mage::throwException(__('The text is too long.'));
        }

        $this->setUserValue($value);
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && strlen($this->getUserValue()) > 0) {
            return $this->getUserValue();
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $value Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($value)
    {
        return \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($value);
    }
}
