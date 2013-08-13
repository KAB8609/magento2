<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Download controller
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Controller_Download extends Magento_Core_Controller_Front_Action
{

    /**
     * Return core session object
     *
     * @return Magento_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Core_Model_Session');
    }

    /**
     * Return customer session object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('Magento_Downloadable_Helper_Download');
        /* @var $helper Magento_Downloadable_Helper_Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }

    /**
     * Download sample action
     *
     */
    public function sampleAction()
    {
        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        $sample = Mage::getModel('Magento_Downloadable_Model_Sample')->load($sampleId);
        if ($sample->getId()) {
            $resource = '';
            $resourceType = '';
            if ($sample->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $sample->getSampleUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($sample->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('Magento_Downloadable_Helper_File')->getFilePath(
                    Magento_Downloadable_Model_Sample::getBasePath(), $sample->getSampleFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError(Mage::helper('Magento_Downloadable_Helper_Data')->__('Sorry, there was an error getting requested content. Please contact the store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link's sample action
     *
     */
    public function linkSampleAction()
    {
        $linkId = $this->getRequest()->getParam('link_id', 0);
        $link = Mage::getModel('Magento_Downloadable_Model_Link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getSampleUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('Magento_Downloadable_Helper_File')->getFilePath(
                    Magento_Downloadable_Model_Link::getBaseSamplePath(), $link->getSampleFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Magento_Core_Exception $e) {
                $this->_getCustomerSession()->addError(Mage::helper('Magento_Downloadable_Helper_Data')->__('Sorry, there was an error getting requested content. Please contact the store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link action
     */
    public function linkAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $linkPurchasedItem = Mage::getModel('Magento_Downloadable_Model_Link_Purchased_Item')->load($id, 'link_hash');
        if (! $linkPurchasedItem->getId() ) {
            $this->_getCustomerSession()->addNotice(Mage::helper('Magento_Downloadable_Helper_Data')->__("We can't find the link you requested."));
            return $this->_redirect('*/customer/products');
        }
        if (!Mage::helper('Magento_Downloadable_Helper_Data')->getIsShareable($linkPurchasedItem)) {
            $customerId = $this->_getCustomerSession()->getCustomerId();
            if (!$customerId) {
                $product = Mage::getModel('Magento_Catalog_Model_Product')->load($linkPurchasedItem->getProductId());
                if ($product->getId()) {
                    $notice = Mage::helper('Magento_Downloadable_Helper_Data')->__('Please log in to download your product or purchase <a href="%s">%s</a>.', $product->getProductUrl(), $product->getName());
                } else {
                    $notice = Mage::helper('Magento_Downloadable_Helper_Data')->__('Please log in to download your product.');
                }
                $this->_getCustomerSession()->addNotice($notice);
                $this->_getCustomerSession()->authenticate($this);
                $this->_getCustomerSession()->setBeforeAuthUrl(Mage::getUrl('downloadable/customer/products/'),
                    array('_secure' => true)
                );
                return ;
            }
            $linkPurchased = Mage::getModel('Magento_Downloadable_Model_Link_Purchased')->load($linkPurchasedItem->getPurchasedId());
            if ($linkPurchased->getCustomerId() != $customerId) {
                $this->_getCustomerSession()->addNotice(Mage::helper('Magento_Downloadable_Helper_Data')->__("We can't find the link you requested."));
                return $this->_redirect('*/customer/products');
            }
        }
        $downloadsLeft = $linkPurchasedItem->getNumberOfDownloadsBought()
            - $linkPurchasedItem->getNumberOfDownloadsUsed();

        $status = $linkPurchasedItem->getStatus();
        if ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
            && ($downloadsLeft || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)
        ) {
            $resource = '';
            $resourceType = '';
            if ($linkPurchasedItem->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $linkPurchasedItem->getLinkUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($linkPurchasedItem->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('Magento_Downloadable_Helper_File')->getFilePath(
                    Magento_Downloadable_Model_Link::getBasePath(), $linkPurchasedItem->getLinkFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                $linkPurchasedItem->setNumberOfDownloadsUsed($linkPurchasedItem->getNumberOfDownloadsUsed() + 1);

                if ($linkPurchasedItem->getNumberOfDownloadsBought() != 0 && !($downloadsLeft - 1)) {
                    $linkPurchasedItem->setStatus(Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
                }
                $linkPurchasedItem->save();
                exit(0);
            }
            catch (Exception $e) {
                $this->_getCustomerSession()->addError(
                    Mage::helper('Magento_Downloadable_Helper_Data')->__('Something went wrong while getting the requested content.')
                );
            }
        } elseif ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            $this->_getCustomerSession()->addNotice(Mage::helper('Magento_Downloadable_Helper_Data')->__('The link has expired.'));
        } elseif ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING
            || $status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
        ) {
            $this->_getCustomerSession()->addNotice(Mage::helper('Magento_Downloadable_Helper_Data')->__('The link is not available.'));
        } else {
            $this->_getCustomerSession()->addError(
                Mage::helper('Magento_Downloadable_Helper_Data')->__('Something went wrong while getting the requested content.')
            );
        }
        return $this->_redirect('*/customer/products');
    }

}
