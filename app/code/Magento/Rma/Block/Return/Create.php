<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Return_Create extends Magento_Rma_Block_Form
{
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Rma_Model_ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var Magento_Rma_Model_Item_FormFactory
     */
    protected $_formFactory;

    /**
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Rma_Model_ItemFactory $itemFactory
     * @param Magento_Rma_Model_Item_FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Rma_Model_ItemFactory $itemFactory,
        Magento_Rma_Model_Item_FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        $this->_itemFactory = $itemFactory;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            return;
        }
        $this->setOrder($order);

        $items = $this->_rmaData->getOrderItems($order);
        $this->setItems($items);

        $session = $this->_session;
        $formData = $session->getRmaFormData(true);
        if (!empty($formData)) {
            $data = new Magento_Object();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $session->getRmaErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new Magento_Object();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  $item | Magento_Sales_Model_Order_Item
     * @return int
     */
    public function getAvailableQty($item)
    {
        $return = $item->getAvailableQty();
        if (!$item->getIsQtyDecimal()) {
            $return = intval($return);
        }
        return $return;
    }



    public function getBackUrl()
    {
        return $this->_urlBuilder->getUrl('sales/order/history');
    }


    /**
     * Prepare rma item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel Magento_Rma_Model_Item */
        $itemModel = $this->_itemFactory->create();

        /* @var $itemForm Magento_Rma_Model_Item_Form */
        $itemForm = $this->_formFactory->create();
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        // prepare item attributes to show
        $attributes = array();

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Magento_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute Magento_Rma_Model_Item_Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        uasort($attributes, array($this, '_compareSortOrder'));

        return $attributes;
    }

    /**
     * Retrieves Contact Email Address on error
     *
     * @return string
     */
    public function getContactEmail()
    {
        $data   = $this->getFormData();
        $email  = '';

        if ($data) {
            $email = $this->escapeHtml($data->getCustomerCustomEmail());
        }
        return $email;
    }

    /**
     * Compares sort order of attributes, returns -1, 0 or 1 if $a sort
     * order is less, equal or greater than $b sort order respectively.
     *
     * @param $a Magento_Rma_Model_Item_Attribute
     * @param $b Magento_Rma_Model_Item_Attribute
     *
     * @return int
     */
    protected function _compareSortOrder(Magento_Rma_Model_Item_Attribute $a, Magento_Rma_Model_Item_Attribute $b)
    {
        $diff = $a->getSortOrder() - $b->getSortOrder();
        return $diff ? ($diff > 0 ? 1 : -1) : 0;
    }
}
