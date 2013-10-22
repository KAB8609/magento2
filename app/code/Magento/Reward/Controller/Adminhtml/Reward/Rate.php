<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward admin rate controller
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Controller\Adminhtml\Reward;

class Rate extends \Magento\Backend\Controller\Adminhtml\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check if module functionality enabled
     *
     * @return \Magento\Reward\Controller\Adminhtml\Reward\Rate
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento\Reward\Helper\Data')->isEnabled()
            && $this->getRequest()->getActionName() != 'noroute'
        ) {
            $this->_forward('noroute');
        }
        return $this;
    }

    /**
     * Initialize layout, breadcrumbs
     *
     * @return \Magento\Reward\Controller\Adminhtml\Reward\Rate
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Reward::customer_reward')
            ->_addBreadcrumb(__('Customers'),
                __('Customers'))
            ->_addBreadcrumb(__('Manage Reward Exchange Rates'),
                __('Manage Reward Exchange Rates'));
        return $this;
    }

    /**
     * Initialize rate object
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    protected function _initRate()
    {
        $this->_title(__('Reward Exchange Rates'));

        $rateId = $this->getRequest()->getParam('rate_id', 0);
        $rate = $this->_objectManager->create('Magento\Reward\Model\Reward\Rate');
        if ($rateId) {
            $rate->load($rateId);
        }
        $this->_coreRegistry->register('current_reward_rate', $rate);
        return $rate;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title(__('Reward Exchange Rates'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New Action.
     * Forward to Edit Action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     */
    public function editAction()
    {
        $rate = $this->_initRate();

        $this->_title($rate->getRateId() ? sprintf("#%s", $rate->getRateId()) : __('New Reward Exchange Rate'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('rate');

        if ($data) {
            $rate = $this->_initRate();

            if ($this->getRequest()->getParam('rate_id') && ! $rate->getId()) {
                return $this->_redirect('*/*/');
            }

            $rate->addData($data);

            try {
                $rate->save();
                $this->_getSession()->addSuccess(__('You saved the rate.'));
            } catch (\Exception $exception) {
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($exception);
                $this->_getSession()->addError(__('We cannot save Rate.'));
                return $this->_redirect('*/*/edit', array('rate_id' => $rate->getId(), '_current' => true));
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Delete Action
     */
    public function deleteAction()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->_getSession()->addSuccess(__('You deleted the rate.'));
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * Validate Action
     *
     */
    public function validateAction()
    {
        $response = new \Magento\Object(array('error' => false));
        $post     = $this->getRequest()->getParam('rate');
        $message  = null;
        /** @var \Magento\Core\Model\StoreManagerInterface $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManagerInterface');
        if ($storeManager->isSingleStoreMode()) {
            $post['website_id'] = $storeManager->getStore(true)->getWebsiteId();
        }

        if (!isset($post['customer_group_id'])
            || !isset($post['website_id'])
            || !isset($post['direction'])
            || !isset($post['value'])
            || !isset($post['equal_value'])) {
            $message = __('Please enter all Rate information.');
        } elseif ($post['direction'] == \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
                  && ((int) $post['value'] <= 0 || (float) $post['equal_value'] <= 0)) {
              if ((int) $post['value'] <= 0) {
                  $message = __('Please enter a positive integer number in the left rate field.');
              } else {
                  $message = __('Please enter a positive number in the right rate field.');
              }
        } elseif ($post['direction'] == \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS
                  && ((float) $post['value'] <= 0 || (int) $post['equal_value'] <= 0)) {
              if ((int) $post['equal_value'] <= 0) {
                  $message = __('Please enter a positive integer number in the right rate field.');
              } else {
                  $message = __('Please enter a positive number in the left rate field.');
              }
        } else {
            $rate       = $this->_initRate();
            $isRateUnique = $rate->getIsRateUniqueToCurrent(
                $post['website_id'],
                $post['customer_group_id'],
                $post['direction']
            );

            if (!$isRateUnique) {
                $message = __('Sorry, but a rate with the same website, customer group and direction or covering rate already exists.');
            }
        }

        if ($message) {
            $this->_getSession()->addError($message);
            $this->_initLayoutMessages('Magento\Adminhtml\Model\Session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reward::rates');
    }
}
