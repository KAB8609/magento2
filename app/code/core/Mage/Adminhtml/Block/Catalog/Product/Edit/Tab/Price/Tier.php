<?php
/**
 * Adminhtml tier pricing item renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier extends Mage_Core_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element = null;
	
	public function __construct() 
	{
		$this->setTemplate('catalog/product/edit/price/tier.phtml');
	}
	
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
	}
	
	public function setElement(Varien_Data_Form_Element_Abstract $element)
	{
		$this->_element = $element;
		return $this;
	}
	
	public function getElement()
	{
		return $this->_element;
	}
	
	public function getValues()
	{
	    $values =array();
	    $data = $this->getElement()->getValue();
		if(is_array($data)) {
			foreach ($data as $value) {
				if (isset($value['price'])) {
				    $value['price'] = number_format($value['price'], 2, null, '');
				}
				$values[] = $value;
			}
		}
		return $values;
	}
	
	protected function _initChildren()
	{
		$this->setChild('delete_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => __('Delete Tier'),
                    'onclick'   => "tierPriceControl.deleteItem('#{index}')",
                    'class' => 'delete'
				)));
				
		$this->setChild('add_button',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => __('Add Tier'),
                    'onclick'   => 'tierPriceControl.addItem()',
                    'class' => 'add'
				)));
	}
	
	public function getAddButtonHtml() 
	{
		return $this->getChildHtml('add_button');
	}
	
	public function getDeleteButtonHtml() 
	{
		return $this->getChildHtml('delete_button');
	}
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier END