<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller for mass opertions with items
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model;

class MassOperations
{
    /**
     * \Zend_Db_Statement_Exception code for "Duplicate unique index" error
     *
     * @var int
     */
    const ERROR_CODE_SQL_UNIQUE_INDEX = 23000;

    /**
     * Whether general error information were added
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Process locking flag
     *
     * @var \Magento\GoogleShopping\Model\Flag
     */
    protected $_flag;

    /**
     * Set process locking flag.
     *
     * @param \Magento\GoogleShopping\Model\Flag $flag
     * @return \Magento\GoogleShopping\Model\MassOperations
     */
    public function setFlag(\Magento\GoogleShopping\Model\Flag $flag)
    {
        $this->_flag = $flag;
        return $this;
    }

    /**
     * Add product to Google Content.
     *
     * @param array $productIds
     * @param int $storeId
     * @throws \Zend_Gdata_App_CaptchaRequiredException
     * @throws \Magento\Core\Exception
     * @return \Magento\GoogleShopping\Model\MassOperations
     */
    public function addProducts($productIds, $storeId)
    {
        $totalAdded = 0;
        $errors = array();
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                try {
                    $product = \Mage::getModel('Magento\Catalog\Model\Product')
                        ->setStoreId($storeId)
                        ->load($productId);

                    if ($product->getId()) {
                        \Mage::getModel('Magento\GoogleShopping\Model\Item')
                            ->insertItem($product)
                            ->save();
                        // The product was added successfully
                        $totalAdded++;
                    }
                } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
                    throw $e;
                } catch (\Zend_Gdata_App_Exception $e) {
                    $errors[] = \Mage::helper('Magento\GoogleShopping\Helper\Data')->parseGdataExceptionMessage($e->getMessage(), $product);
                } catch (\Zend_Db_Statement_Exception $e) {
                    $message = $e->getMessage();
                    if ($e->getCode() == self::ERROR_CODE_SQL_UNIQUE_INDEX) {
                        $message = __("The Google Content item for product '%1' (in '%2' store) already exists.", $product->getName(), \Mage::app()->getStore($product->getStoreId())->getName());
                    }
                    $errors[] = $message;
                } catch (\Magento\Core\Exception $e) {
                    $errors[] = __('The product "%1" cannot be added to Google Content. %2', $product->getName(), $e->getMessage());
                } catch (\Exception $e) {
                    \Mage::logException($e);
                    $errors[] = __('The product "%1" hasn\'t been added to Google Content.', $product->getName());
                }
            }
            if (empty($productIds)) {
                return $this;
            }
        }

        if ($totalAdded > 0) {
            $this->_getNotifier()->addNotice(
                __('Products were added to Google Shopping account.'),
                __('A total of %1 product(s) have been added to Google Content.', $totalAdded)
            );
        }

        if (count($errors)) {
            $this->_getNotifier()->addMajor(
                __('Errors happened while adding products to Google Shopping.'),
                $errors
            );
        }

        if ($this->_flag->isExpired()) {
            $this->_getNotifier()->addMajor(
                __('Operation of adding products to Google Shopping expired.'),
                __('Some products may have not been added to Google Shopping bacause of expiration')
            );
        }

        return $this;
    }

    /**
     * Update Google Content items.
     *
     * @param array|\Magento\GoogleShopping\Model\Resource\Item\Collection $items
     * @throws \Zend_Gdata_App_CaptchaRequiredException
     * @throws \Magento\Core\Exception
     * @return \Magento\GoogleShopping\Model\MassOperations
     */
    public function synchronizeItems($items)
    {
        $totalUpdated = 0;
        $totalDeleted = 0;
        $totalFailed = 0;
        $errors = array();

        $itemsCollection = $this->_getItemsCollection($items);

        if ($itemsCollection) {
            if (count($itemsCollection) < 1) {
                return $this;
            }
            foreach ($itemsCollection as $item) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                try {
                    $item->updateItem();
                    $item->save();
                    // The item was updated successfully
                    $totalUpdated++;
                } catch (\Magento\Gdata\Gshopping\HttpException $e) {
                    if (in_array('notfound', $e->getCodes())) {
                        $item->delete();
                        $totalDeleted++;
                    } else {
                        $this->_addGeneralError();
                        $errors[] = \Mage::helper('Magento\GoogleShopping\Helper\Data')
                            ->parseGdataExceptionMessage($e->getMessage(), $item->getProduct());
                        $totalFailed++;
                    }
                } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
                    throw $e;
                } catch (\Zend_Gdata_App_Exception $e) {
                    $this->_addGeneralError();
                    $errors[] = \Mage::helper('Magento\GoogleShopping\Helper\Data')
                        ->parseGdataExceptionMessage($e->getMessage(), $item->getProduct());
                    $totalFailed++;
                } catch (\Magento\Core\Exception $e) {
                    $errors[] = __('The item "%1" cannot be updated at Google Content. %2', $item->getProduct()->getName(), $e->getMessage());
                    $totalFailed++;
                } catch (\Exception $e) {
                    \Mage::logException($e);
                    $errors[] = __('The item "%1" hasn\'t been updated.', $item->getProduct()->getName());
                    $totalFailed++;
                }
            }
        } else {
            return $this;
        }

        $this->_getNotifier()->addNotice(
            __('Product synchronization with Google Shopping completed'),
            __('A total of %1 items(s) have been deleted; a total of %2 items(s) have been updated.', $totalDeleted, $totalUpdated)
        );
        if ($totalFailed > 0 || count($errors)) {
            array_unshift($errors, __("We cannot update %1 items.", $totalFailed));
            $this->_getNotifier()->addMajor(
                __('Errors happened during synchronization with Google Shopping'),
                $errors
            );
        }

        return $this;
    }

    /**
     * Remove Google Content items.
     *
     * @param array|\Magento\GoogleShopping\Model\Resource\Item\Collection $items
     * @throws \Zend_Gdata_App_CaptchaRequiredException
     * @return \Magento\GoogleShopping\Model\MassOperations
     */
    public function deleteItems($items)
    {
        $totalDeleted = 0;
        $itemsCollection = $this->_getItemsCollection($items);
        $errors = array();
        if ($itemsCollection) {
            if (count($itemsCollection) < 1) {
                return $this;
            }
            foreach ($itemsCollection as $item) {
                if ($this->_flag && $this->_flag->isExpired()) {
                    break;
                }
                try {
                    $item->deleteItem()->delete();
                    // The item was removed successfully
                    $totalDeleted++;
                } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
                    throw $e;
                } catch (\Zend_Gdata_App_Exception $e) {
                    $this->_addGeneralError();
                    $errors[] = \Mage::helper('Magento\GoogleShopping\Helper\Data')
                        ->parseGdataExceptionMessage($e->getMessage(), $item->getProduct());
                } catch (\Exception $e) {
                    \Mage::logException($e);
                    $errors[] = __('The item "%1" hasn\'t been deleted.', $item->getProduct()->getName());
                }
            }
        } else {
            return $this;
        }

        if ($totalDeleted > 0) {
            $this->_getNotifier()->addNotice(
                __('Google Shopping item removal process succeded'),
                __('Total of %1 items(s) have been removed from Google Shopping.', $totalDeleted)
            );
        }
        if (count($errors)) {
            $this->_getNotifier()->addMajor(
                __('Errors happened while deleting items from Google Shopping'),
                $errors
            );
        }

        return $this;
    }

    /**
     * Return items collection by IDs
     *
     * @param array|\Magento\GoogleShopping\Model\Resource\Item\Collection $items
     * @throws \Magento\Core\Exception
     * @return null|\Magento\GoogleShopping\Model\Resource\Item\Collection
     */
    protected function _getItemsCollection($items)
    {
        $itemsCollection = null;
        if ($items instanceof \Magento\GoogleShopping\Model\Resource\Item\Collection) {
            $itemsCollection = $items;
        } else if (is_array($items)) {
            $itemsCollection = \Mage::getResourceModel('Magento\GoogleShopping\Model\Resource\Item\Collection')
                ->addFieldToFilter('item_id', $items);
        }

        return $itemsCollection;
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return \Magento\Adminhtml\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Adminhtml\Model\Session');
    }

    /**
     * Retrieve admin notifier
     *
     * @return Magento_Adminhtml_Model_Inbox
     */
    protected function _getNotifier()
    {
        return \Mage::getModel('Magento\AdminNotification\Model\Inbox');
    }

    /**
     * Provides general error information
     */
    protected function _addGeneralError()
    {
        if (!$this->_hasError) {
            $this->_getNotifier()->addMajor(
                __('Google Shopping Error'),
                \Mage::helper('Magento\GoogleShopping\Helper\Category')->getMessage()
            );
            $this->_hasError = true;
        }
    }
}
