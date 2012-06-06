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
 * Adminhtml customer tax class controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Tax_Class_CustomerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * grid view
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Customer Tax Classes'));

        $this->_initAction()
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Tax_Class')
                    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            )
            ->renderLayout();
    }

    /**
     * new class action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit class action
     *
     */
    public function editAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Customer Tax Classes'));

        $classId    = $this->getRequest()->getParam('id');
        $model      = Mage::getModel('Mage_Tax_Model_Class');
        if ($classId) {
            $model->load($classId);
            if (!$model->getId() || $model->getClassType() != Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('This class no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getClassName() : $this->__('New Class'));

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getClassData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('tax_class', $model);

        $this->_initAction()
            ->_addBreadcrumb(
                $classId ? Mage::helper('Mage_Tax_Helper_Data')->__('Edit Class') :  Mage::helper('Mage_Tax_Helper_Data')->__('New Class'),
                $classId ?  Mage::helper('Mage_Tax_Helper_Data')->__('Edit Class') :  Mage::helper('Mage_Tax_Helper_Data')->__('New Class')
            )
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Tax_Class_Edit')
                    ->setData('action', $this->getUrl('*/tax_class/save'))
                    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
            )
            ->renderLayout();
    }

    /**
     * delete class action
     *
     */
    public function deleteAction()
    {
        $classId    = $this->getRequest()->getParam('id');
        $session    = Mage::getSingleton('Mage_Adminhtml_Model_Session');
        $classModel = Mage::getModel('Mage_Tax_Model_Class')
            ->load($classId);

        if (!$classModel->getId() || $classModel->getClassType() != Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('This class no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $ruleCollection = Mage::getModel('Mage_Tax_Model_Calculation_Rule')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER, $classId);

        if ($ruleCollection->getSize() > 0) {
            $session->addError(Mage::helper('Mage_Tax_Helper_Data')->__('You cannot delete this tax class as it is used in Tax Rules. You have to delete the rules it is used in first.'));
            $this->_redirect('*/*/edit/',array('id'=>$classId));
            return;
        }

        $customerGroupCollection = Mage::getModel('Mage_Customer_Model_Group')
            ->getCollection()
            ->addFieldToFilter('tax_class_id', $classId);
        $groupCount = $customerGroupCollection->getSize();

        if ($groupCount > 0) {
            $session->addError(Mage::helper('Mage_Tax_Helper_Data')->__('You cannot delete this tax class as it is used for %d customer groups.', $groupCount));
            $this->_redirect('*/*/edit/',array('id'=>$classId));
            return;
        }

        try {
            $classModel->delete();

            $session->addSuccess(Mage::helper('Mage_Tax_Helper_Data')->__('The tax class has been deleted.'));
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return ;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('Mage_Tax_Helper_Data')->__('An error occurred while deleting this tax class.'));
        }

        $this->_redirect('*/*/edit/',array('id'=>$classId));
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Tax::sales_tax_classes_customer')
            ->_addBreadcrumb(Mage::helper('Mage_Tax_Helper_Data')->__('Sales'), Mage::helper('Mage_Tax_Helper_Data')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('Mage_Tax_Helper_Data')->__('Tax'), Mage::helper('Mage_Tax_Helper_Data')->__('Tax'))
            ->_addBreadcrumb(Mage::helper('Mage_Tax_Helper_Data')->__('Manage Customer Tax Classes'), Mage::helper('Mage_Tax_Helper_Data')->__('Manage Customer Tax Classes'))
        ;
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/tax/classes_customer');
    }
}
