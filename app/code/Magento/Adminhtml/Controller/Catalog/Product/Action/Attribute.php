<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml catalog product action attribute update controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Catalog\Product\Action;

class Attribute extends \Magento\Adminhtml\Controller\Action
{
    public function editAction()
    {
        if (!$this->_validateProducts()) {
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Update product attributes
     */
    public function saveAction()
    {
        if (!$this->_validateProducts()) {
            return;
        }

        /* Collect Data */
        $inventoryData      = $this->getRequest()->getParam('inventory', array());
        $attributesData     = $this->getRequest()->getParam('attributes', array());
        $websiteRemoveData  = $this->getRequest()->getParam('remove_website_ids', array());
        $websiteAddData     = $this->getRequest()->getParam('add_website_ids', array());

        /* Prepare inventory data item options (use config settings) */
        foreach (\Mage::helper('Magento\CatalogInventory\Helper\Data')->getConfigItemOptions() as $option) {
            if (isset($inventoryData[$option]) && !isset($inventoryData['use_config_' . $option])) {
                $inventoryData['use_config_' . $option] = 0;
            }
        }

        try {
            if ($attributesData) {
                $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
                $storeId    = $this->_getHelper()->getSelectedStoreId();

                foreach ($attributesData as $attributeCode => $value) {
                    $attribute = \Mage::getSingleton('Magento\Eav\Model\Config')
                        ->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    if ($attribute->getBackendType() == 'datetime') {
                        if (!empty($value)) {
                            $filterInput    = new \Zend_Filter_LocalizedToNormalized(array(
                                'date_format' => $dateFormat
                            ));
                            $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
                                'date_format' => \Magento\Date::DATE_INTERNAL_FORMAT
                            ));
                            $value = $filterInternal->filter($filterInput->filter($value));
                        } else {
                            $value = null;
                        }
                        $attributesData[$attributeCode] = $value;
                    } elseif ($attribute->getFrontendInput() == 'multiselect') {
                        // Check if 'Change' checkbox has been checked by admin for this attribute
                        $isChanged = (bool)$this->getRequest()->getPost($attributeCode . '_checkbox');
                        if (!$isChanged) {
                            unset($attributesData[$attributeCode]);
                            continue;
                        }
                        if (is_array($value)) {
                            $value = implode(',', $value);
                        }
                        $attributesData[$attributeCode] = $value;
                    }
                }

                \Mage::getSingleton('Magento\Catalog\Model\Product\Action')
                    ->updateAttributes($this->_getHelper()->getProductIds(), $attributesData, $storeId);
            }
            if ($inventoryData) {
                $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item');
                $stockItem->setProcessIndexEvents(false);
                $stockItemSaved = false;

                foreach ($this->_getHelper()->getProductIds() as $productId) {
                    $stockItem->setData(array());
                    $stockItem->loadByProduct($productId)
                        ->setProductId($productId);

                    $stockDataChanged = false;
                    foreach ($inventoryData as $k => $v) {
                        $stockItem->setDataUsingMethod($k, $v);
                        if ($stockItem->dataHasChangedFor($k)) {
                            $stockDataChanged = true;
                        }
                    }
                    if ($stockDataChanged) {
                        $stockItem->save();
                        $stockItemSaved = true;
                    }
                }

                if ($stockItemSaved) {
                    \Mage::getSingleton('Magento\Index\Model\Indexer')->indexEvents(
                        \Magento\CatalogInventory\Model\Stock\Item::ENTITY,
                        \Magento\Index\Model\Event::TYPE_SAVE
                    );
                }
            }

            if ($websiteAddData || $websiteRemoveData) {
                /* @var $actionModel \Magento\Catalog\Model\Product\Action */
                $actionModel = \Mage::getSingleton('Magento\Catalog\Model\Product\Action');
                $productIds  = $this->_getHelper()->getProductIds();

                if ($websiteRemoveData) {
                    $actionModel->updateWebsites($productIds, $websiteRemoveData, 'remove');
                }
                if ($websiteAddData) {
                    $actionModel->updateWebsites($productIds, $websiteAddData, 'add');
                }

                $this->_eventManager->dispatch('catalog_product_to_website_change', array(
                    'products' => $productIds
                ));

                $this->_getSession()->addNotice(
                    __('Please refresh "Catalog URL Rewrites" and "Product Attributes" in System -> <a href="%1">Index Management</a>.', $this->getUrl('adminhtml/process/list'))
                );
            }

            $this->_getSession()->addSuccess(
                __('A total of %1 record(s) were updated.', count($this->_getHelper()->getProductIds()))
            );
        }
        catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) attributes.'));
        }

        $this->_redirect('*/catalog_product/', array('store'=>$this->_getHelper()->getSelectedStoreId()));
    }

    /**
     * Validate selection of products for massupdate
     *
     * @return boolean
     */
    protected function _validateProducts()
    {
        $error = false;
        $productIds = $this->_getHelper()->getProductIds();
        if (!is_array($productIds)) {
            $error = __('Please select products for attributes update.');
        } else if (!\Mage::getModel('Magento\Catalog\Model\Product')->isProductsHasSku($productIds)) {
            $error = __('Please make sure to define SKU values for all processed products.');
        }

        if ($error) {
            $this->_getSession()->addError($error);
            $this->_redirect('*/catalog_product/', array('_current'=>true));
        }

        return !$error;
    }

    /**
     * Rertive data manipulation helper
     *
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Edit\Action\Attribute
     */
    protected function _getHelper()
    {
        return \Mage::helper('Magento\Adminhtml\Helper\Catalog\Product\Edit\Action\Attribute');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::update_attributes');
    }

    /**
     * Attributes validation action
     *
     */
    public function validateAction()
    {
        $response = new \Magento\Object();
        $response->setError(false);
        $attributesData = $this->getRequest()->getParam('attributes', array());
        $data = new \Magento\Object();

        try {
            if ($attributesData) {
                $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
                $storeId    = $this->_getHelper()->getSelectedStoreId();

                foreach ($attributesData as $attributeCode => $value) {
                    $attribute = \Mage::getSingleton('Magento\Eav\Model\Config')
                        ->getAttribute('catalog_product', $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    $data->setData($attributeCode, $value);
                    $attribute->getBackend()->validate($data);
                }
            }
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) attributes.'));
            $this->_initLayoutMessages('Magento\Adminhtml\Model\Session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
}
