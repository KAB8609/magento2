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
 * Adminhtml common tax class controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Tax_Class extends Mage_Adminhtml_Controller_Action
{
    /**
     * Save Tax Class via AJAX
     */
    public function ajaxSaveAction()
    {
        $responseContent = '';
        try {
            $classData = array(
                'class_id' => (int)$this->getRequest()->getPost('class_id') ?: null, // keep null for new tax classes
                'class_type' => $this->_processClassType((string)$this->getRequest()->getPost('class_type')),
                'class_name' => $this->_processClassName((string)$this->getRequest()->getPost('class_name'))
            );
            $class = Mage::getModel('Mage_Tax_Model_Class')
                ->setData($classData)
                ->save();
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'class_id' => $class->getId(),
                'class_name' => $class->getClassName()
            ));
        } catch (Mage_Core_Exception $e) {
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => $e->getMessage(),
                'class_id' => '',
                'class_name' => ''
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Tax_Helper_Data') ->__('Something went wrong saving this tax class.'),
                'class_id' => '',
                'class_name' => ''
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Delete Tax Class via AJAX
     */
    public function ajaxDeleteAction()
    {
        $classId = (int)$this->getRequest()->getParam('class_id');
        try {
            /** @var $classModel Mage_Tax_Model_Class */
            $classModel = $this->_objectManager->create('Mage_Tax_Model_Class')->load($classId);
            $classModel->checkClassCanBeDeleted();
            $classModel->delete();
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => ''
            ));
        } catch (Mage_Core_Exception $e) {
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => $e->getMessage()
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Tax_Helper_Data')->__('Something went wrong deleting this tax class.')
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Validate/Filter Tax Class Type
     *
     * @param string $classType
     * @return string processed class type
     * @throws Mage_Core_Exception
     */
    protected function _processClassType($classType)
    {
        $validClassTypes = array(
            Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
            Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT
        );
        if (!in_array($classType, $validClassTypes)) {
            Mage::throwException(Mage::helper('Mage_Tax_Helper_Data') ->__('Invalid type of tax class specified.'));
        }
        return $classType;
    }

    /**
     * Validate/Filter Tax Class Name
     *
     * @param string $className
     * @return string processed class name
     * @throws Mage_Core_Exception
     */
    protected function _processClassName($className)
    {
        $className = trim(Mage::helper('Mage_Tax_Helper_Data')->escapeHtml($className));
        if ($className == '') {
            Mage::throwException(Mage::helper('Mage_Tax_Helper_Data') ->__('Invalid name of tax class specified.'));
        }
        return $className;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Tax::manage_tax');
    }
}