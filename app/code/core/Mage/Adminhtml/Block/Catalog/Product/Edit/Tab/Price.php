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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml product edit price block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$product = Mage::registry('product');

		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('tiered_price', array('legend'=>Mage::helper('catalog')->__('Tier Pricing')));

		$fieldset->addField('default_price', 'label', array(
				'label'=> Mage::helper('catalog')->__('Default price'),
				'title'=> Mage::helper('catalog')->__('Default price'),
				'name'=>'default_price',
				'bold'=>true,
				'value'=>$product->getPrice()
		));

		$fieldset->addField('tier_price', 'text', array(
				'name'=>'tier_price',
				'class'=>'requried-entry',
				'value'=>$product->getData('tier_price')
		));


		$form->getElement('tier_price')->setRenderer(
			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
		);

		$this->setForm($form);
	}
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price END