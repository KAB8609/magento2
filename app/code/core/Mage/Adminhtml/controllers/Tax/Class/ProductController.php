<?php
/**
 * Adminhtml tax class product controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_Class_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax_rule'));
        $this->_addBreadcrumb(__('Product tax classes'), __('Product tax classes title'));

        $this->_addTabs();

        $this->_addContent(
        		$this->getLayout()->createBlock('adminhtml/tax_class_toolbar_add')
        		->assign('createUrl', Mage::getUrl('adminhtml/tax_class_product/add'))
        		->assign('header', __('Product tax classes'))
        	);

        $grid = $this->getLayout()->createBlock('adminhtml/tax_class_grid_default');
        $grid->setClassType('PRODUCT');
        $this->_addContent($grid);

        $this->renderLayout();
    }

    public function addAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax'));
        $this->_addBreadcrumb(__('Product tax classes'), __('Product tax classes title'), Mage::getUrl('adminhtml/tax_class_product'));
        $this->_addBreadcrumb(__('New product tax class'), __('New product tax class title'));

        $this->_addTabs();

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/tax_class_toolbar_save')
            ->assign('header', __('New product tax class'))
        );
        $this->_addContent($this->getLayout()->createBlock('adminhtml/tax_class_product_form_add'));

        $this->renderLayout();
    }

    protected function _addTabs($tabId='tax_class_product')
    {
        $tabs = $this->getLayout()->createBlock('adminhtml/tax_tabs')
            ->setActiveTab($tabId);
        $this->_addLeft($tabs);
    }
}