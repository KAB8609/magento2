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
 * Poll edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Rating_Edit_Tab_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Store manager instance
     *
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }


    /**
     * Prepare rating edit form
     *
     * @return Mage_Adminhtml_Block_Rating_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('rating_form', array(
            'legend'=>Mage::helper('Mage_Rating_Helper_Data')->__('Rating Title')
        ));

        $fieldset->addField('rating_code', 'text', array(
            'name' => 'rating_code',
            'label' => Mage::helper('Mage_Rating_Helper_Data')->__('Default Value'),
            'class' => 'required-entry',
            'required' => true,
        ));

        foreach (Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreCollection() as $store) {
            $fieldset->addField('rating_code_' . $store->getId(), 'text', array(
                'label' => $store->getName(),
                'name' => 'rating_codes[' . $store->getId() . ']',
            ));
        }

        if (Mage::getSingleton('Mage_Adminhtml_Model_Session')->getRatingData()) {
            $form->setValues(Mage::getSingleton('Mage_Adminhtml_Model_Session')->getRatingData());
            $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getRatingData();
            if (isset($data['rating_codes'])) {
               $this->_setRatingCodes($data['rating_codes']);
            }
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setRatingData(null);
        } elseif (Mage::registry('rating_data')) {
            $form->setValues(Mage::registry('rating_data')->getData());
            if (Mage::registry('rating_data')->getRatingCodes()) {
               $this->_setRatingCodes(Mage::registry('rating_data')->getRatingCodes());
            }
        }

        if (Mage::registry('rating_data')) {
            $collection = Mage::getModel('Mage_Rating_Model_Rating_Option')
                ->getResourceCollection()
                ->addRatingFilter(Mage::registry('rating_data')->getId())
                ->load();

            $i = 1;
            foreach ($collection->getItems() as $item) {
                $fieldset->addField('option_code_' . $item->getId() , 'hidden', array(
                    'required' => true,
                    'name' => 'option_title[' . $item->getId() . ']',
                    'value' => ($item->getCode()) ? $item->getCode() : $i,
                ));

                $i ++;
            }
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $fieldset->addField('option_code_' . $i, 'hidden', array(
                    'required' => true,
                    'name' => 'option_title[add_' . $i . ']',
                    'value' => $i,
                ));
            }
        }

        $fieldset = $form->addFieldset('visibility_form', array(
            'legend' => Mage::helper('Mage_Rating_Helper_Data')->__('Rating Visibility')
        ));
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('stores', 'multiselect', array(
                'label' => Mage::helper('Mage_Rating_Helper_Data')->__('Visible In'),
                'name' => 'stores[]',
                'values' => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);

            if (Mage::registry('rating_data')) {
                $form->getElement('stores')->setValue(Mage::registry('rating_data')->getStores());
            }
        }

        $fieldset->addField('is_active', 'checkbox', array(
            'label' => Mage::helper('Mage_Rating_Helper_Data')->__('Is Active'),
            'name' => 'is_active',
            'value' => 1,
        ));

        $fieldset->addField('position', 'text', array(
            'label' => Mage::helper('Mage_Rating_Helper_Data')->__('Sort Order'),
            'name' => 'position',
        ));

        if (Mage::registry('rating_data')) {
            $form->getElement('position')->setValue(Mage::registry('rating_data')->getPosition());
            $form->getElement('is_active')->setIsChecked(Mage::registry('rating_data')->getIsActive());
        }

        return parent::_prepareForm();
    }

    protected function _setRatingCodes($ratingCodes) {
        foreach($ratingCodes as $store=>$value) {
            if($element = $this->getForm()->getElement('rating_code_' . $store)) {
               $element->setValue($value);
            }
        }
    }

    protected function _toHtml()
    {
        return $this->_getWarningHtml() . parent::_toHtml();
    }

    protected function _getWarningHtml()
    {
        return '<div>
<ul class="messages">
    <li class="notice-msg">
        <ul>
            <li>'.Mage::helper('Mage_Rating_Helper_Data')->__('Please specify a rating title for a store, or we\'ll just use the default value.').'</li>
        </ul>
    </li>
</ul>
</div>';
    }


}
