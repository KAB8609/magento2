<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom variable model
 *
 * @method Mage_Core_Model_Resource_Variable _getResource()
 * @method Mage_Core_Model_Resource_Variable getResource()
 * @method string getCode()
 * @method Mage_Core_Model_Variable setCode(string $value)
 * @method string getName()
 * @method Mage_Core_Model_Variable setName(string $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Variable extends Mage_Core_Model_Abstract
{
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';

    protected $_storeId = 0;

    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Core_Model_Resource_Variable');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return Mage_Core_Model_Variable
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Load variable by code
     *
     * @param string $code
     * @return Mage_Core_Model_Variable
     */
    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Return variable value depend on given type
     *
     * @param string $type
     * @return string
     */
    public function getValue($type = null)
    {
        if ($type === null) {
            $type = self::TYPE_HTML;
        }
        if ($type == self::TYPE_TEXT || !(strlen((string)$this->getData('html_value')))) {
            $value = $this->getData('plain_value');
            //escape html if type is html, but html value is not defined
            if ($type == self::TYPE_HTML) {
                $value = nl2br(Mage::helper('Mage_Core_Helper_Data')->escapeHtml($value));
            }
            return $value;
        }
        return $this->getData('html_value');
    }

    /**
     * Validation of object data. Checking for unique variable code
     *
     * @return boolean | string
     */
    public function validate()
    {
        if ($this->getCode() && $this->getName()) {
            $variable = $this->getResource()->getVariableByCode($this->getCode());
            if (!empty($variable) && $variable['variable_id'] != $this->getId()) {
                return Mage::helper('Mage_Core_Helper_Data')->__('Variable Code must be unique.');
            }
            return true;
        }
        return Mage::helper('Mage_Core_Helper_Data')->__('Validation has failed.');
    }

    /**
     * Retrieve variables option array
     *
     * @param boolean $withValues
     * @return array
     */
    public function getVariablesOptionArray($withGroup = false)
    {
        /* @var $collection Mage_Core_Model_Resource_Variable_Collection */
        $collection = $this->getCollection();
        $variables = array();
        foreach ($collection->toOptionArray() as $variable) {
            $variables[] = array(
                'value' => '{{customVar code=' . $variable['value'] . '}}',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('%1', $variable['label'])
            );
        }
        if ($withGroup && $variables) {
            $variables = array(
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Custom Variables'),
                'value' => $variables
            );
        }
        return $variables;
    }

}
