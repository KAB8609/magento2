<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TargetRule_Block_Adminhtml_Product extends Magento_Backend_Block_Widget
{
    /**
     * Attributes is read only flag
     *
     * @var bool
     */
    protected $_readOnly = false;

    /**
     * Target rule data
     *
     * @var Magento_TargetRule_Helper_Data
     */
    protected $_targetRuleData = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_TargetRule_Helper_Data $targetRuleData
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_TargetRule_Model_Source_Position
     */
    protected $_position;

    /**
     * @param Magento_TargetRule_Model_Source_Position $position
     * @param Magento_TargetRule_Helper_Data $targetRuleData
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_TargetRule_Model_Source_Position $position,
        Magento_TargetRule_Helper_Data $targetRuleData,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_position = $position;
        $this->_coreRegistry = $registry;
        $this->_targetRuleData = $targetRuleData;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve Product List Type by current Form Prefix
     *
     * @return int
     */
    protected function _getProductListType()
    {
        $listType = '';
        switch ($this->getFormPrefix()) {
            case 'related':
                $listType = Magento_TargetRule_Model_Rule::RELATED_PRODUCTS;
                break;
            case 'upsell':
                $listType = Magento_TargetRule_Model_Rule::UP_SELLS;
                break;
        }
        return $listType;
    }

    /**
     * Retrieve current edit product instance
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Get data for Position Behavior selector
     *
     * @return array
     */
    public function getPositionBehaviorOptions()
    {
        return $this->_position->toOptionArray();
    }

    /**
     * Get value of Rule Based Positions
     *
     * @return mixed
     */
    public function getPositionLimit()
    {
        $position = $this->_getValue('position_limit');
        if (is_null($position)) {
            $position = $this->_targetRuleData->getMaximumNumberOfProduct($this->_getProductListType());
        }
        return $position;
    }

    /**
     * Get value of Position Behavior
     *
     * @return mixed
     */
    public function getPositionBehavior()
    {
        $show = $this->_getValue('position_behavior');
        if (is_null($show)) {
            $show = $this->_targetRuleData->getShowProducts($this->_getProductListType());
        }
        return $show;
    }

    /**
     * Get value from Product model
     *
     * @param string $var
     * @return mixed
     */
    protected function _getValue($field)
    {
        return $this->getProduct()->getDataUsingMethod($this->getFieldName($field));
    }

    /**
     * Get name of the field
     *
     * @param string $field
     * @return string
     */
    public function getFieldName($field)
    {
        return $this->getFormPrefix() . '_tgtr_' . $field;
    }

    /**
     * Define is value should me marked as default
     *
     * @param string $value
     * @return bool
     */
    public function isDefault($value)
    {
        return ($this->_getValue($value) === null) ? true : false;
    }

    /**
     * Set TargetRule Attributes is ReadOnly
     *
     * @param bool $flag
     * @return Magento_TargetRule_Block_Adminhtml_Product
     */
    public function setIsReadonly($flag)
    {
        return $this->setData('is_readonly', (bool)$flag);
    }

    /**
     * Retrieve TargetRule Attributes is ReadOnly flag
     * Default return false if does not exists any instruction
     *
     * @return bool
     */
    public function getIsReadonly()
    {
        $flag = $this->_getData('is_readonly');
        if (is_null($flag)) {
            $flag = false;
        }
        return $flag;
    }

    /**
     * Get is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
