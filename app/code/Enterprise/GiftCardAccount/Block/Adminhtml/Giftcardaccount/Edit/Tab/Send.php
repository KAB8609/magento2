<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Init form fields
     *
     * @return Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Edit_Tab_Send
     */
    public function initForm()
    {
        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('_send');

        $model = Mage::registry('current_giftcardaccount');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Send Gift Card'))
        );

        $fieldset->addField('recipient_email', 'text', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient Email'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient Email'),
            'class'     => 'validate-email',
            'name'      => 'recipient_email',
        ));

        $fieldset->addField('recipient_name', 'text', array(
            'label'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient Name'),
            'title'     => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Recipient Name'),
            'name'      => 'recipient_name',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'     => 'recipient_store',
                'label'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Send Email from the Following Store View'),
                'title'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Send Email from the Following Store View'),
                'after_element_html' => $this->_getStoreIdScript()
            ));
            $renderer = $this->getLayout()
                ->createBlock('Mage_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('action', 'hidden', array(
            'name'      => 'send_action',
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }

    protected function _getStoreIdScript()
    {
        $websiteStores = array();
        foreach (Mage::app()->getWebsites() as $websiteId => $website) {
            $websiteStores[$websiteId] = array();
            foreach ($website->getGroups() as $groupId => $group) {
                $websiteStores[$websiteId][$groupId] = array(
                    'name' => $group->getName()
                );
                foreach ($group->getStores() as $storeId => $store) {
                    $websiteStores[$websiteId][$groupId]['stores'][] = array(
                        'id'   => $storeId,
                        'name' => $store->getName(),
                    );
                }
            }
        }

        $websiteStores = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($websiteStores);

        $result  = '<script type="text/javascript">//<![CDATA[' . "\n";
        $result .= "var websiteStores = $websiteStores;";
        $result .= "Event.observe('_infowebsite_id', 'change', setCurrentStores);";
        $result .= "setCurrentStores();";
        $result .= 'function setCurrentStores(){
            var wSel = $("_infowebsite_id");
            var sSel = $("_sendstore_id");

            sSel.innerHTML = \'\';
            var website = wSel.options[wSel.selectedIndex].value;
            if (websiteStores[website]) {
                groups = websiteStores[website];
                for (groupKey in groups) {
                    group = groups[groupKey];
                    optionGroup = document.createElement("OPTGROUP");
                    optionGroup.label = group["name"];
                    sSel.appendChild(optionGroup);

                    stores = group["stores"];
                    for (i=0; i < stores.length; i++) {
                        var option = document.createElement("option");
                        option.appendChild(document.createTextNode(stores[i]["name"]));
                        option.setAttribute("value", stores[i]["id"]);
                        optionGroup.appendChild(option);
                    }
                }
            }
            else {
              var option = document.createElement("option");
              option.appendChild(document.createTextNode(\''.Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('-- First Please Select a Website --').'\'));
              sSel.appendChild(option);
            }
        }
        //]]></script>';

        return $result;
    }
}
