<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Prepare form
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('fieldLogo', array('legend' => $this->__('Logo')));
        $this->_addElementTypes($fieldset);
        $this->addImage($fieldset, 'conf[native][navigationBar][icon]', 'Logo in header');
        $this->addImage($fieldset, 'conf[native][body][bannerImage]', 'Home screen');

        $fieldset = $form->addFieldset('fieldColors', array('legend' => $this->__('Color Themes')));
        $this->_addElementTypes($fieldset);
        // FIXME: radio buttons width presets
        $this->addColor($fieldset, 'conf[special][headerBackgroundColor]', $this->__('Header Background Color'));
        $this->addColor($fieldset, 'conf[special][headerTextColor]', $this->__('Header Text'));
        $this->addColor($fieldset, 'conf[special][primaryBodyColor]', $this->__('Primary'));
        $this->addColor($fieldset, 'conf[special][secondaryBodyColor]', $this->__('Secondary'));
        $this->addColor($fieldset, 'conf[special][bodyTextColor]', $this->__('Body Text'));
        $this->addColor($fieldset, 'conf[special][priceTextColor]', $this->__('Price Text'));

        /*
        $fieldset = $form->addFieldset('fieldFonts', array('legend' => $this->__('Fonts')));
        $this->_addElementTypes($fieldset);
        $this->addFontSimple($fieldset, 'conf[special][headerTextFont]');
        $this->addFontSimple($fieldset, 'conf[special][bodyTextFont]');
        */

        /*
        $fieldset = $form->addFieldset('fieldTabs', array('legend' => $this->__('Tabs')));
        // FIXME
        */

        $model = Mage::registry('current_app');
        $form->setValues($model->getFormData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
