<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_template = 'catalog/product/edit.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_edit');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add elements in layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit
     */
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Back'),
                'onclick' => 'setLocation(\''
                    . $this->getUrl('*/*/', array('store' => $this->getRequest()->getParam('store', 0))) . '\')',
                'class' => 'action-back'
            ));
        } else {
            $this->addChild('back_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                'onclick' => 'window.close()',
                'class' => 'cancel'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('reset_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('_current' => true)) . '\')'
            ));
        }

        if (!$this->getRequest()->getParam('popup')) {
            if ($this->getProduct()->isDeleteable()) {
                $this->addChild('delete_button', 'Mage_Adminhtml_Block_Widget_Button', array(
                    'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Delete'),
                    'onclick' => 'confirmSetLocation(\''
                        . Mage::helper('Mage_Catalog_Helper_Data')->__('Are you sure?') . '\', \'' . $this->getDeleteUrl() . '\')',
                    'class' => 'delete'
                ));
            }
        }
        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('save-split-button', 'Mage_Backend_Block_Widget_Button_Split', array(
                'id' => 'save-split-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save'),
                'class_name' => 'Mage_Backend_Block_Widget_Button_Split',
                'button_class' => 'widget-button-save',
                'options' => $this->_getSaveSplitButtonOptions()
            ));
        }

        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    /**
     * Get Save Split Button html
     *
     * @return string
     */
    public function getSaveSplitButtonHtml()
    {
        return $this->getChildHtml('save-split-button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }

    public function getIsGrouped()
    {
        return $this->getProduct()->isGrouped();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }

    public function getHeader()
    {
        if ($this->getProduct()->getId()) {
            $header = $this->escapeHtml($this->getProduct()->getName());
        } else {
            $header = Mage::helper('Mage_Catalog_Helper_Data')->__('New Product');
        }
        return $header;
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    public function getIsConfigured()
    {
        $result = true;

        $product = $this->getProduct();
        if ($product->isConfigurable()) {
            $superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds($product);
            $result = !empty($superAttributes);
        }

        return $result;
    }

    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }

    /**
     * Get fields masks from config
     *
     * @return array
     */
    public function getFieldsAutogenerationMasks()
    {
        return $this->helper('Mage_Catalog_Helper_Product')->getFieldsAutogenerationMasks();
    }

    /**
     * Retrieve available placeholders
     *
     * @return array
     */
    public function getAttributesAllowedForAutogeneration()
    {
        return $this->helper('Mage_Catalog_Helper_Product')->getAttributesAllowedForAutogeneration();
    }

    /**
     * Get data for JS (product type transition)
     *
     * @return string
     */
    public function getTypeSwitcherData()
    {
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
            'tab_id' => 'product_info_tabs_downloadable_items',
            'is_virtual_id' => Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Weight::VIRTUAL_FIELD_HTML_ID,
            'weight_id' => 'weight',
            'current_type' => $this->getProduct()->getTypeId(),
            'attributes' => $this->_getAttributes(),
        ));
    }

    /**
     * Get formed array with attribute codes and Apply To property
     *
     * @return array
     */
    protected function _getAttributes()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getProduct();
        $attributes = array();

        foreach ($product->getAttributes() as $key => $attribute) {
            $attributes[$key] = $attribute->getApplyTo();
        }
        return $attributes;
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {
        $options = array();
        if (!$this->getRequest()->getParam('popup')) {
            $options[] = array(
                'id' => 'edit-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Edit'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#product-edit-form'),
                    ),
                ),
                'default' => true,
            );
        }

        /** @var $limitation Mage_Catalog_Model_Product_Limitation */
        $limitation = Mage::getObjectManager()->get('Mage_Catalog_Model_Product_Limitation');
        if ($this->_isProductNew()) {
            $showAddNewButtons = !$limitation->isCreateRestricted(2);
        } else {
            $showAddNewButtons = !$limitation->isCreateRestricted();
        }
        if ($showAddNewButtons) {
            $options[] = array(
                'id' => 'new-button',
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & New'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndNew', 'target' => '#product-edit-form'),
                    ),
                ),
            );
            if (!$this->getRequest()->getParam('popup') && $this->getProduct()->isDuplicable()) {
                $options[] = array(
                    'id' => 'duplicate-button',
                    'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Duplicate'),
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array('event' => 'saveAndClone', 'target' => '#product-edit-form'),
                        ),
                    ),
                );
            }
        }
        $options[] = array(
            'id' => 'close-button',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Save & Close'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#product-edit-form'),
                ),
            ),
        );
        return $options;
    }

    /**
     * Check whether new product is being created
     *
     * @return bool
     */
    protected function _isProductNew()
    {
        $product = $this->getProduct();
        return !$product || !$product->getId();
    }
}
