<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profiles view/management controller
 *
 * TODO: implement ACL restrictions
 */
namespace Magento\Sales\Controller\Adminhtml\Recurring;

use Magento\App\Action\NotFoundException;

class Profile extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\App\Action\Title $title
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\App\Action\Title $title
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_title = $title;
    }

    /**
     * Recurring profiles list
     */
    public function indexAction()
    {
        $this->_title->add(__('Recurring Billing Profiles'))
            ->loadLayout()
            ->_setActiveMenu('Magento_Sales::sales_recurring_profile')
            ->renderLayout();
        return $this;
    }

    /**
     * View recurring profile details
     */
    public function viewAction()
    {
        try {
            $this->_title->add(__('Recurring Billing Profiles'));
            $profile = $this->_initProfile();
            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::sales_recurring_profile')
                ->_title->add(__('Profile #%1', $profile->getReferenceId()))
                ->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('sales/*/');
    }

    /**
     * Profiles ajax grid
     */
    public function gridAction()
    {
        try {
            $this->loadLayout()->renderLayout();
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('sales/*/');
    }

    /**
     * Profile orders ajax grid
     *
     * @throws NotFoundException
     */
    public function ordersAction()
    {
        try {
            $this->_initProfile();
            $this->loadLayout()->renderLayout();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            throw new NotFoundException();
        }
    }

    /**
     * Profile state updater action
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $profile->cancel();
                    break;
                case 'suspend':
                    $profile->suspend();
                    break;
                case 'activate':
                    $profile->activate();
                    break;
            }
            $this->_getSession()->addSuccess(__('The profile state has been updated.'));
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We could not update the profile.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($profile) {
            $this->_redirect('sales/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('sales/*/');
        }
    }

    /**
     * Profile information updater action
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->_getSession()->addSuccess(__('You updated the profile.'));
            } else {
                $this->_getSession()->addNotice(__('The profile has no changes.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addError(__('We could not update the profile.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        if ($profile) {
            $this->_redirect('sales/*/view', array('profile' => $profile->getId()));
        } else {
            $this->_redirect('sales/*/');
        }
    }

    /**
     * Customer billing agreements ajax action
     *
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return \Magento\Sales\Controller\Adminhtml\Billing\Agreement
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        $this->_coreRegistry->register('current_customer', $customer);
        return $this;
    }

    /**
     * Load/set profile
     *
     * @return \Magento\Sales\Model\Recurring\Profile
     */
    protected function _initProfile()
    {
        $profile = $this->_objectManager->create('Magento\Sales\Model\Recurring\Profile')->load($this->getRequest()->getParam('profile'));
        if (!$profile->getId()) {
            throw new \Magento\Core\Exception(__('The profile you specified does not exist.'));
        }
        $this->_coreRegistry->register('current_recurring_profile', $profile);
        return $profile;
    }
}
