<?php
/**
 * Customer address controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Default account page
     *
     */
    public function indexAction() 
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }

        // Load addresses
        $addressCollection = new Mage_Customer_Address_Collection();
        $addressCollection->loadByCustomer(Mage_Customer_Front::getCustomerId());
        
        $block = Mage::createBlock('tpl', 'customer.address')
            ->setViewName('Mage_Customer', 'address.phtml')
            ->assign('primaryAddresses', $addressCollection->getPrimaryTypes())
            ->assign('alternativeAddresses', $addressCollection->getPrimaryTypes(false));
        
        Mage::getBlock('content')->append($block);
    }
    
    /**
     * Address book form
     *
     */
    public function formAction()
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }
        
        // TODO: restore form data and messages from session        
        $addressId = $this->getRequest()->getParam('address', false);
        
        if ($addressId) {
            $address = new Mage_Customer_Address($addressId);
            
            // Validate address_id <=> customer_id
            if ($address->getCustomerId()!=Mage_Customer_Front::getCustomerId()) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            
            $primary = $address->getPrimaryTypes();
        } else {
            $address = new Mage_Customer_Address();
            $primary = array();
        }

        $types = Mage::getResourceModel('customer', 'address_type')->getAvailableTypes();
        foreach ($types as $typeCode=>$type) {
            $types[$typeCode]['name'] = $type['address_type_name'];
            $types[$typeCode]['is_primary'] = !empty($primary[$typeCode]);
        }
            
        $block = Mage::createBlock('tpl', 'customer.address.form')
            ->setViewName('Mage_Customer', 'form/address.phtml')
            ->assign('formData', $address)
            ->assign('primaryTypes', $types);
            
        Mage::getBlock('content')->append($block);
    }
    
    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $addressValidator = new Mage_Customer_Validate_Address($_POST);
            
            // Validate address_id <=> customer_id
            if (!$address->hasCustomer(Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            
            $data = $addressValidator->getData();
            
            // Validate data
            if ($addressValidator->isValid()) {
                $addressModel = Mage::getResourceModel('customer', 'address');
                
                if ($data['address_id']) {
                    $saveRes = $addressModel->update($data, $data['address_id']);
                } else {
                    $data['customer_id'] = Mage_Customer_Front::getCustomerId();
                    $addressModel->insert($data);
                }
                
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
            }
            else {
                // validation error message
                $formData = $addressValidator->getDataObject();
            }
        }
    }
    
    public function deleteAction()
    {
        if (!Mage_Customer_Front::authenticate($this)) {
            return;
        }

        $addressId = $this->getRequest()->getParam('address', false);
        $addressValidator = new Mage_Customer_Validate_Address(array());
        
        if ($addressId) {
            
            // Validate address_id <=> customer_id
            if (!$addressValidator->hasCustomer($addressId, Mage_Customer_Front::getCustomerId())) {
                $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
                return;
            }
            $addressModel = Mage::getResourceModel('customer', 'address');
            $addressModel->delete($addressId);
        }
        
        $this->_redirect(Mage::getBaseUrl('', 'Mage_Customer').'/address/');
    }
}// Class Mage_Customer_AccountController END
