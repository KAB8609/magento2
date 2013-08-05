<?php
/**
 * {license_notice}
 *
 * @category
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * TheFind feed product grid controller
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Adminhtml_Items_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Main index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Find_Feed_Block_Adminhtml_List_Items_Grid')->toHtml()
        );
    }

    /**
     * Product list for mass action
     *
     * @return array
     */
    protected function _getMassActionProducts()
    {
        $idList = $this->getRequest()->getParam('item_id');
        if (!empty($idList)) {
            $products = array();
            foreach ($idList as $id) {
                $model = Mage::getModel('Mage_Catalog_Model_Product');
                if ($model->load($id)) {
                    array_push($products, $model);
                }
            }
            return $products;
        } else {
            return array();
        }
    }

    /**
     * Add product to feed mass action
     */
    public function massEnableAction()
    {
        $idList = $this->getRequest()->getParam('item_id');
        $updateAction = Mage::getModel('Mage_Catalog_Model_Product_Action');
        $attrData = array(
            'is_imported' => 1
        );
        $updatedProducts = count($idList);
        if ($updatedProducts) {
            try {
                $updateAction->updateAttributes($idList, $attrData, Mage::app()->getStore()->getId());
                Mage::getModel('Find_Feed_Model_Import')->processImport();
                $this->_getSession()->addSuccess(__("%s product in feed", $updatedProducts));
            } catch (Exception $e) {
                $this->_getSession()->addError(__("We are unable to process an import.") . $e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Not add product to feed mass action
     */
    public function massDisableAction()
    {
        $updatedProducts = 0;
        foreach ($this->_getMassActionProducts() as $product) {
            $product->setIsImported(0);
            $product->save();
            $updatedProducts++;
        }
        if ($updatedProducts) {
            Mage::getModel('Find_Feed_Model_Import')->processImport();
            $this->_getSession()->addSuccess(__("%s product not in feed", $updatedProducts));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Find_Feed::import_items');
    }
}
