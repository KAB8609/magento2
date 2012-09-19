<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info extends Mage_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/tab/info.phtml';

    /**
     * Init form fields
     *
     * @return Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Info
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_info');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Information'))
        );

        if ($model->getId()){
            $fieldset->addField('code', 'label', array(
                'name'      => 'code',
                'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Code'),
                'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card Code')
            ));

            $fieldset->addField('state_text', 'label', array(
                'name'      => 'state_text',
                'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Status'),
                'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Status')
            ));
        }

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Active'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Active'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_DISABLED =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('status', Enterprise_GiftCardAccount_Model_Giftcardaccount::STATUS_ENABLED);
        }

        $fieldset->addField('is_redeemable', 'select', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Redeemable'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Redeemable'),
            'name'      => 'is_redeemable',
            'required'  => true,
            'options'   => array(
                Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Yes'),
                Enterprise_GiftCardAccount_Model_Giftcardaccount::NOT_REDEEMABLE =>
                    Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('No'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_redeemable', Enterprise_GiftCardAccount_Model_Giftcardaccount::REDEEMABLE);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Website'),
                'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Website'),
                'required'  => true,
                'values'    => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteValuesForForm(true),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addType('price', 'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Form_Price');

        $note = '';
        if (Mage::app()->isSingleStoreMode()) {
            $currencies = $this->_getCurrency();
            $note = '<b>[' . array_shift($currencies) . ']</b>';
        }
        $fieldset->addField('balance', 'price', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Balance'),
            'name'      => 'balance',
            'class'     => 'validate-number',
            'required'  => true,
            'note'      => '<div id="balance_currency">' . $note . '</div>',
        ));

        $fieldset->addField('date_expires', 'date', array(
            'name'   => 'date_expires',
            'label'  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Expiration Date'),
            'title'  => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Expiration Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

        $form->setValues($model->getData());

        $this->setForm($form);
        return $this;
    }

    /**
     * Get array of base currency codes among all existing web sites
     *
     * @return array
     */
    protected function _getCurrency()
    {
        $result = array();
        $websites = Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteCollection();
        foreach ($websites as $id => $website) {
            $result[$id] = $website->getBaseCurrencyCode();
        }
        return $result;
    }

    /**
     * Encode currency array to Json string
     *
     * @return string
     */
    public function getCurrencyJson()
    {
        $result = $this->_getCurrency();
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }
}
