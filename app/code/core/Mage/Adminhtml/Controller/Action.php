<?php

class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    protected function _construct()
    {
        parent::_construct();

        Mage::getDesign()->setArea('adminhtml')
            ->setPackageName('default')
            ->setTheme('default');

        $this->getLayout()->setArea('adminhtml');   
    }
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        return $this;
    }

    protected function _addBreadcrumb($label, $title, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }

    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('left')->append($block);
        return $this;
    }
    
    protected function _addJs(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('js')->append($block);
        return $this;
    }
    
    protected function _isAllowed()
    {
    	return Mage::getSingleton('admin/session')->isAllowed('all');
    }
    
    public function preDispatch()
    {
    	parent::preDispatch();

    	if ($this->getRequest()->isDispatched() && !$this->_isAllowed()) {
    		$this->_forward('denied', 'index');
    		$this->getRequest()->setDispatched(false);
    	}
    	
    	return $this;
    }
    
    function loadLayout($ids=null, $key='', $generateBlocks=true)
    {
        parent::loadLayout($ids, $key, $generateBlocks);
        $this->_initLayoutMessages('adminhtml/session');
        return $this;
    }
    
    function norouteAction($coreRoute = null)
    {
        $this->loadLayout(array('baseframe', 'admin_noroute'), 'admin_noroute');
        $this->renderLayout();
    }
}