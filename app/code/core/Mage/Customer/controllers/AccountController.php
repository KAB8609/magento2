<?php
/**
 * Customer account controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AccountController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^[create|forgotpassword]#', $action)) {
            if (!Mage::getSingleton('customer_model', 'session')->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
    
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        $block = Mage::createBlock('customer_account', 'customer.account')
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
    }
    
    public function logoutAction()
    {
        Mage::getSingleton('customer_model', 'session')->logout();
        $this->_redirect(Mage::getBaseUrl());
    }
    
    /**
     * Registration form
     *
     */
    public function createAction()
    {
        // if customer logged in
        if (Mage::getSingleton('customer_model', 'session')->isLoggedIn()) {
            $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
        }
        
        $countries = Mage::getModel('directory', 'country_collection');

        $block = Mage::createBlock('tpl', 'customer.regform')
            ->setViewName('Mage_Customer', 'form/registration.phtml')
            ->assign('action',      Mage::getBaseUrl('', 'Mage_Customer') . '/account/createPost/')
            ->assign('countries',   $countries->loadByCurrentDomain())
            ->assign('regions',     $countries->getDefault()->getRegions())
            ->assign('data',        Mage::getSingleton('customer_model', 'session')->getData(true))
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Create account
     */
    public function createPostAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $address  = Mage::getModel('customer', 'address')->setData($this->getRequest()->getPost());
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());

            $customer->addAddress($address);
            
            try {
                $customer->save();
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS001'));
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->setData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/create/');
    }
    
    public function editAction()
    {
        $data = Mage::getSingleton('customer_model', 'session')->getData(true);
        if ($data->isEmpty()) {
            $data = Mage::getSingleton('customer_model', 'session')->getCustomer();
        }
        
        $block = Mage::createBlock('tpl', 'customer.edit')
            ->setViewName('Mage_Customer', 'form/edit.phtml')
            ->assign('action',      Mage::getBaseUrl('', 'Mage_Customer').'/account/editPost/')
            ->assign('data',        $data)
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
    }
    
    public function editPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getModel('customer', 'customer')->setData($this->getRequest()->getPost());
            $customer->setCustomerId(Mage::getSingleton('customer_model', 'session')->getCustomerId());
            
            try {
                $customer->save();
                Mage::getSingleton('customer_model', 'session')
                    ->setCustomer($customer)
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS002'));
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->setData($this->getRequest()->getPost())
                    ->addMessages($e->getMessages());
            }
        }
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/edit/');
    }
    
    /**
     * Change password form
     *
     */
    public function changePasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.changepassword')
            ->setViewName('Mage_Customer', 'form/changepassword.phtml')
            ->assign('action',      Mage::getBaseUrl('', 'Mage_Customer').'/account/changePasswordPost/')
            ->assign('messages',    Mage::getSingleton('customer_model', 'session')->getMessages(true));
            
        Mage::getBlock('content')->append($block);
    }
    
    public function changePasswordPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $customer = Mage::getSingleton('customer_model', 'session')->getCustomer();
            
            try {
                $customer->changePassword($this->getRequest()->getPost());
                
                Mage::getSingleton('customer_model', 'session')
                    ->addMessage(Mage::getModel('customer_model', 'message')->success('CSTS003'));
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer') . '/account/');
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer_model', 'session')
                    ->addMessages($e->getMessages());
            }
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/account/changePassword/');
    }
    
    /**
     * Forgot password
     *
     */
    public function forgotPasswordAction()
    {
        $block = Mage::createBlock('tpl', 'customer.forgotpassword')
            ->setViewName('Mage_Customer', 'form/forgotpassword.phtml');
        Mage::getBlock('content')->append($block);
    }
    
    public function forgotPasswordPostAction()
    {
        
    }

    public function newsletterAction()
    {
        $block = Mage::createBlock('tpl', 'customer.newsletter')
            ->setViewName('Mage_Customer', 'form/newsletter.phtml');
        Mage::getBlock('content')->append($block);
    }
    
    public function newsletterPostAction()
    {
        
    }
}// Class Mage_Customer_AccountController END