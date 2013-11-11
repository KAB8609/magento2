<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ProductAlert controller
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ProductAlert\Controller;

class Add extends \Magento\App\Action\Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!$this->_objectManager->get('Magento\Customer\Model\Session')->getBeforeUrl()) {
                $this->_objectManager->get('Magento\Customer\Model\Session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }

    public function testObserverAction()
    {
        $object = new \Magento\Object();
        $observer = $this->_objectManager->get('Magento\ProductAlert\Model\Observer');
        $observer->process($object);
    }

    public function priceAction()
    {
        $session = $this->_objectManager->get('Magento\Catalog\Model\Session');
        $backUrl    = $this->getRequest()->getParam(\Magento\Core\App\Action\Plugin\LastUrl::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId()) {
            /* @var $product \Magento\Catalog\Model\Product */
            $session->addError(__('There are not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        try {
            $model = $this->_objectManager->create('Magento\ProductAlert\Model\Price')
                ->setCustomerId($this->_objectManager->get('Magento\Customer\Model\Session')->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(
                    $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getWebsiteId()
                );
            $model->save();
            $session->addSuccess(__('You saved the alert subscription.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }

    public function stockAction()
    {
        $session = $this->_objectManager->get('Magento\Catalog\Model\Session');
        /* @var $session \Magento\Catalog\Model\Session */
        $backUrl    = $this->getRequest()->getParam(\Magento\Core\App\Action\Plugin\LastUrl::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        if (!$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId)) {
            /* @var $product \Magento\Catalog\Model\Product */
            $session->addError(__('There are not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = $this->_objectManager->create('Magento\ProductAlert\Model\Stock')
                ->setCustomerId($this->_objectManager->get('Magento\Customer\Model\Session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(
                    $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getWebsiteId()
                );
            $model->save();
            $session->addSuccess(__('Alert subscription has been saved.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }
}
