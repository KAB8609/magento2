<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Item model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Item extends Magento_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY            = 'rma_item';

    /**
     * Rma instance
     *
     * @var Magento_Rma_Model_Rma
     */
    protected $_rma         = null;

    /**
     * Store firstly set all item attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Rma item $_FILES collection
     *
     * @var array
     */
    protected $_filesArray  = array();

    /**
     * Rma item errors
     *
     * @var array
     */
    protected $_errors      = array();

    /**
     * Image url
     */
    const ITEM_IMAGE_URL    = 'rma_item';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Resource_Item');
    }

    /**
     * Declare rma instance
     *
     * @param   Magento_Rma_Model_Rma $rma
     * @return  Magento_Rma_Model_Item
     */
    public function setRma(Magento_Rma_Model_Rma $rma)
    {
        $this->_rma = $rma;
        $this->setRmaEntityId($rma->getId());
        return $this;
    }

    /**
     * Retrieve rma instance
     *
     * @return Magento_Rma_Model_Rma
     */
    public function getRma()
    {
        $rmaId = $this->getRmaEntityId();
        if (is_null($this->_rma) && $rmaId) {
            $rma = Mage::getModel('Magento_Rma_Model_Rma');
            $rma->load($rmaId);
            $this->setRma($rma);
        }
        return $this->_rma;
    }

    /**
     * Get RMA item's status label
     *
     * @return mixed
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            $this->setStatusLabel(
                Mage::getModel('Magento_Rma_Model_Item_Attribute_Source_Status')->getItemLabel($this->getStatus())
            );
        }
        return parent::getStatusLabel();
    }

    /**
     * Prepare data before save
     *
     * @return Magento_Rma_Model_Item
     */
    protected function _beforeSave()
    {
        if (!$this->getRmaEntityId() && $this->getRma()) {
            $this->setRmaEntityId($this->getRma()->getId());
        }
        if ($this->getQtyAuthorized() === '') {
            $this->unsQtyAuthorized();
        }
        if ($this->getQtyReturned() === '') {
            $this->unsQtyReturned();
        }
        if ($this->getQtyApproved() === '') {
            $this->unsQtyApproved();
        }
        parent::_beforeSave();
    }

    /**
     * Prepare data before save
     *
     * @return Magento_Rma_Model_Item
     */
    protected function _afterSave()
    {
        $qtyReturnedChange = 0;
        if ($this->getOrigData('status') == Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED) {
            if ($this->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED) {
                $qtyReturnedChange = $this->getQtyApproved() - $this->getOrigData('qty_approved');
            } else {
                $qtyReturnedChange = - $this->getOrigData('qty_approved');
            }
        } else {
            if ($this->getStatus() == Magento_Rma_Model_Rma_Source_Status::STATE_APPROVED) {
                $qtyReturnedChange = $this->getQtyApproved();
            }
        }

        if ($qtyReturnedChange) {
            $item = Mage::getModel('Magento_Sales_Model_Order_Item')->load($this->getOrderItemId());
            if ($item->getId()) {
                $item->setQtyReturned($item->getQtyReturned() + $qtyReturnedChange)
                    ->save();
            }
        }
        parent::_afterSave();
    }

    /**
     * Retrieve all item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Retrieve all item errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Get rma item attribute model object
     *
     * @param   string $attributeCode
     * @return  Magento_Rma_Model_Item_Attribute | null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Prepares and adds $_POST data to item's attribute
     *
     * @param  array $itemPost
     * @param  int $key
     * @return array
     */
    public function prepareAttributes($itemPost, $key)
    {
        $httpRequest = new Zend_Controller_Request_Http();
        $httpRequest->setPost($itemPost);

        /** @var $itemForm Magento_Rma_Model_Item_Form */
        $itemForm = Mage::getModel('Magento_Rma_Model_Item_Form');
        $itemForm->setFormCode('default')
            ->setEntity($this);

        $itemData = $itemForm->extractData($httpRequest);

        $files = array();
        foreach ($itemData as $code=>&$value) {
            if (is_array($value) && empty($value)) {
                if (array_key_exists($code.'_'.$key, $_FILES)) {
                    $value = $_FILES[$code.'_'.$key];
                    $files[] = $code;
                }
            }
        }

        $itemErrors = $itemForm->validateData($itemData);
        if ($itemErrors !== true) {
            $this->_errors = array_merge($itemErrors, $this->_errors);
        } else {
            $itemForm->compactData($itemData);
        }

        if (!empty($files)) {
            foreach ($files as $code) {
                unset($_FILES[$code.'_'.$key]);
            }
            return $files;
        }
    }

    /**
     * Gets item options
     *
     * @return array|bool
     */
    public function getOptions()
    {
        $result = array();
        $options = unserialize($this->getProductOptions());
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            return $result;
        }
        return false;
    }

}
