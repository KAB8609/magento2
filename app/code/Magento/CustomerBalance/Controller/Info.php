<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customerbalance controller for My Account
 *
 */
namespace Magento\CustomerBalance\Controller;

class Info extends \Magento\App\Action\Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Store Credit dashboard
     *
     */
    public function indexAction()
    {
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_redirect('customer/account/');
            return;
        }
        $this->loadLayout();
        $this->getLayout()->initMessages('Magento\Customer\Model\Session');
        $this->loadLayoutUpdates();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Store Credit'));
        }
        $this->renderLayout();
    }
}
