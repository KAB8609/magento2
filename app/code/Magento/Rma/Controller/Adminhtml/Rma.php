<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Controller_Adminhtml_Rma extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Session $session
     * @param Magento_Backend_Model_Session $backendSession
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Session $session,
        Magento_Backend_Model_Session $backendSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session = $session;
        $this->_backendSession = $backendSession;
        parent::__construct($context);
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return Magento_Rma_Controller_Adminhtml_Rma
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Rma::sales_magento_rma_rma');

        $this->_title(__('Returns'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return Magento_Rma_Model_Rma
     * @throws Magento_Core_Exception
     */
    protected function _initModel($requestParam = 'id')
    {
        /** @var $model Magento_Rma_Model_Rma */
        $model = $this->_objectManager->create('Magento_Rma_Model_Rma');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $rmaId = $this->getRequest()->getParam($requestParam);
        if ($rmaId) {
            $model->load($rmaId);
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('The wrong RMA was requested.'));
            }
            $this->_coreRegistry->register('current_rma', $model);
            $orderId = $model->getOrderId();
        } else {
            $orderId = $this->getRequest()->getParam('order_id');
        }

        if ($orderId) {
            /** @var $order Magento_Sales_Model_Order */
            $order = $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);
            if (!$order->getId()) {
                throw new Magento_Core_Exception(__('This is the wrong RMA order ID.'));
            }
            $this->_coreRegistry->register('current_order', $order);
        }

        return $model;
    }

    /**
     * Initialize model
     *
     * @return Magento_Rma_Model_Rma_Create
     */
    protected function _initCreateModel()
    {
        /** @var $model Magento_Rma_Model_Rma_Create */
        $model = $this->_objectManager->create('Magento_Rma_Model_Rma_Create');
        $orderId = $this->getRequest()->getParam('order_id');
        $model->setOrderId($orderId);
        if ($orderId) {
            /** @var $order Magento_Sales_Model_Order */
            $order =  $this->_objectManager->create('Magento_Sales_Model_Order')->load($orderId);
            $model->setCustomerId($order->getCustomerId());
            $model->setStoreId($order->getStoreId());
        }
        $this->_coreRegistry->register('rma_create_model', $model);
        return $model;
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    /**
     * Create new RMA
     *
     * @throws Magento_Core_Exception
     */
    public function newAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $customerId = $this->getRequest()->getParam('customer_id');
            $this->_redirect('*/*/chooseorder', array('customer_id' => $customerId));
        } else {
            try {
                $this->_initCreateModel();
                $this->_initModel();
                if (!$this->_objectManager->get('Magento_Rma_Helper_Data')->canCreateRma($orderId, true)) {
                    $this->_backendSession->addError(
                        __('There are no applicable items for return in this order.')
                    );
                }
            } catch (Magento_Core_Exception $e) {
                $this->_backendSession->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }

            $this->_initAction();
            $this->_title(__('New Return'));
            $this->renderLayout();
        }
    }

    /**
     * Choose Order action during new RMA creation
     */
    public function chooseorderAction()
    {
        $this->_initCreateModel();

        $this->_initAction()
            ->_title(__('New Return'))
            ->renderLayout();
    }

    /**
     * Edit RMA
     *
     * @throws Magento_Core_Exception
     */
    public function editAction()
    {
        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('The wrong RMA was requested.'));
            }
        } catch (Magento_Core_Exception $e) {
            $this->_backendSession->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }
        $this->_initAction();
        $this->_title(sprintf("#%s", $model->getIncrementId()));
        $this->renderLayout();
    }

    /**
     * Save new RMA request
     *
     * @throws Magento_Core_Exception
     */
    public function saveNewAction()
    {
        if (!$this->getRequest()->isPost() || $this->getRequest()->getParam('back', false)) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            /** @var $model Magento_Rma_Model_Rma */
            $model = $this->_initModel();
            $saveRequest = $this->_filterRmaSaveRequest($this->getRequest()->getPost());
            $model->setData($this->_prepareNewRmaInstanceData($saveRequest));
            if (!$model->saveRma($saveRequest)) {
                throw new Magento_Core_Exception(__('We failed to save this RMA.'));
            }
            $this->_processNewRmaAdditionalInfo($saveRequest, $model);
            $this->_backendSession->addSuccess(__('You submitted the RMA request.'));
        } catch (Magento_Core_Exception $e) {
            $this->_backendSession->addError($e->getMessage());
            $errorKeys = $this->_session->getRmaErrorKeys();
            $controllerParams = array('order_id' => $this->_coreRegistry->registry('current_order')->getId());
            if (!empty($errorKeys) && isset($errorKeys['tabs']) && ($errorKeys['tabs'] == 'items_section')) {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('*/*/new', $controllerParams);
            return;
        } catch (Exception $e) {
            $this->_backendSession->addError(__('We failed to save this RMA.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Prepare RMA instance data from save request
     *
     * @param array $saveRequest
     * @return array
     */
    protected function _prepareNewRmaInstanceData(array $saveRequest)
    {
        $order = $this->_coreRegistry->registry('current_order');
        /** @var $dateModel Magento_Core_Model_Date */
        $dateModel = $this->_objectManager->get('Magento_Core_Model_Date');
        $rmaData = array(
            'status' => Magento_Rma_Model_Rma_Source_Status::STATE_PENDING,
            'date_requested' => $dateModel->gmtDate(),
            'order_id' => $order->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'store_id' => $order->getStoreId(),
            'customer_id' => $order->getCustomerId(),
            'order_date' => $order->getCreatedAt(),
            'customer_name' => $order->getCustomerName(),
            'customer_custom_email' => !empty($saveRequest['contact_email']) ? $saveRequest['contact_email'] : ''
        );
        return $rmaData;
    }

    /**
     * Process additional RMA information (like comment, customer notification etc)
     *
     * @param array $saveRequest
     * @param Magento_Rma_Model_Rma $rma
     * @return Magento_Rma_Controller_Adminhtml_Rma
     */
    protected function _processNewRmaAdditionalInfo(array $saveRequest, Magento_Rma_Model_Rma $rma)
    {
        if (!empty($saveRequest['comment']['comment'])) {
            $visible = isset($saveRequest['comment']['is_visible_on_front']);
            /** @var $dateModel Magento_Core_Model_Date */
            $dateModel = $this->_objectManager->get('Magento_Core_Model_Date');
            /** @var $statusHistory Magento_Rma_Model_Rma_Status_History */
            $statusHistory = $this->_objectManager->create('Magento_Rma_Model_Rma_Status_History');
            $statusHistory->setRmaEntityId($rma->getId())
                ->setComment($saveRequest['comment']['comment'])
                ->setIsVisibleOnFront($visible)
                ->setStatus($rma->getStatus())
                ->setCreatedAt($dateModel->gmtDate())
                ->setIsAdmin(1)
                ->save();
        }
        if (!empty($saveRequest['rma_confirmation'])) {
            $rma->sendNewRmaEmail();
        }
        return $this;
    }

    /**
     * Save RMA request
     *
     * @throws Magento_Core_Exception
     */
    public function saveAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('*/*/');
            return;
        }
        $rmaId = (int)$this->getRequest()->getParam('rma_id');
        if (!$rmaId) {
            $this->saveNewAction();
            return;
        }
        try {
            $saveRequest = $this->_filterRmaSaveRequest($this->getRequest()->getPost());
            $itemStatuses = $this->_combineItemStatuses($saveRequest['items'], $rmaId);
            $model = $this->_initModel('rma_id');
            /** @var $sourceStatus Magento_Rma_Model_Rma_Source_Status */
            $sourceStatus = $this->_objectManager->create('Magento_Rma_Model_Rma_Source_Status');
            $model->setStatus($sourceStatus->getStatusByItems($itemStatuses))
                ->setIsUpdate(1);
            if (!$model->saveRma($saveRequest)) {
                throw new Magento_Core_Exception(__('We failed to save this RMA.'));
            }
            $model->sendAuthorizeEmail();
            $this->_backendSession->addSuccess(__('You saved the RMA request.'));
            $redirectBack = $this->getRequest()->getParam('back', false);
            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $rmaId, 'store' => $model->getStoreId()));
                return;
            }
        } catch (Magento_Core_Exception $e) {
            $this->_backendSession->addError($e->getMessage());
            $errorKeys = $this->_session->getRmaErrorKeys();
            $controllerParams = array('id' => $rmaId);
            if (isset($errorKeys['tabs']) && ($errorKeys['tabs'] == 'items_section')) {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('*/*/edit', $controllerParams);
            return;
        } catch (Exception $e) {
            $this->_backendSession->addError(__('We failed to save this RMA.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Filter RMA save request
     *
     * @param array $saveRequest
     * @return array
     * @throws Magento_Core_Exception
     */
    protected function _filterRmaSaveRequest(array $saveRequest)
    {
        if (!isset($saveRequest['items'])) {
            throw new Magento_Core_Exception(__('We failed to save this RMA. No items have been specified.'));
        }
        $saveRequest['items'] = $this->_filterRmaItems($saveRequest['items']);
        return $saveRequest;
    }

    /**
     * Filter user provided RMA items
     *
     * @param array $rawItems
     * @return array
     */
    protected function _filterRmaItems(array $rawItems)
    {
        $items = array();
        foreach ($rawItems as $key => $itemData) {
            if (!isset($itemData['qty_authorized'])
                && !isset($itemData['qty_returned'])
                && !isset($itemData['qty_approved'])
                && !isset($itemData['qty_requested'])
            ) {
                continue;
            }
            $itemData['entity_id'] = (strpos($key, '_') === false) ? $key : false;
            $items[$key] = $itemData;
        }
        return $items;
    }

    /**
     * Combine item statuses from POST request items and original RMA items
     *
     * @param array $requestedItems
     * @param int $rmaId
     * @return array
     */
    protected function _combineItemStatuses(array $requestedItems, $rmaId)
    {
        $statuses = array();
        foreach ($requestedItems as $requestedItem) {
            if (isset($requestedItem['status'])) {
                array_push($statuses, $requestedItem['status']);
            }
        }
        /* Merge RMA Items status with POST data*/
        /** @var $rmaItems Magento_Rma_Model_Resource_Item_Collection */
        $rmaItems = $this->_objectManager->create('Magento_Rma_Model_Resource_Item_Collection')
            ->addAttributeToFilter('rma_entity_id', $rmaId);
        foreach ($rmaItems as $rmaItem) {
            if (!isset($requestedItems[$rmaItem->getId()])) {
                array_push($statuses, $rmaItem->getStatus());
            }
        }
        return $statuses;
    }

    /**
     * Delete rma
     */
    public function deleteAction()
    {
        $this->_redirect('*/*/');
    }

    /**
     * Close action for rma
     */
    public function closeAction(){
        $entityId = $this->getRequest()->getParam('entity_id');
        if ($entityId) {
            $entityId = intval($entityId);
            $entityIds = array($entityId);
            $returnRma = $entityId;
        } else {
            $entityIds = $this->getRequest()->getPost('entity_ids', array());
            $returnRma = null;
        }
        $countCloseRma = 0;
        $countNonCloseRma = 0;
        foreach ($entityIds as $entityId) {
            /** @var $rma Magento_Rma_Model_Rma */
            $rma = $this->_objectManager->create('Magento_Rma_Model_Rma')->load($entityId);
            if ($rma->canClose()) {
                $rma->close()
                    ->save();
                $countCloseRma++;
            } else {
                $countNonCloseRma++;
            }
        }
        if ($countNonCloseRma) {
            if ($countCloseRma) {
                $this->_getSession()->addError(__('%1 RMA(s) cannot be closed', $countNonCloseRma));
            } else {
                $this->_getSession()->addError(__('We cannot close the RMA request(s).'));
            }
        }
        if ($countCloseRma) {
            $this->_getSession()->addSuccess(__('%1 RMA (s) have been closed.', $countCloseRma));
        }

        if ($returnRma) {
            $this->_redirect('*/*/edit', array('id' => $returnRma));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Add RMA comment action
     *
     * @throws Magento_Core_Exception
     * @return void
     */
    public function addCommentAction()
    {
        try {
            $this->_initModel();

            $data = $this->getRequest()->getPost('comment');
            $notify = isset($data['is_customer_notified']);
            $visible = isset($data['is_visible_on_front']);

            $rma = $this->_coreRegistry->registry('current_rma');
            if (!$rma) {
                throw new Magento_Core_Exception(__('Invalid RMA'));
            }

            $comment = trim($data['comment']);
            if (!$comment) {
                throw new Magento_Core_Exception(__('Please enter a valid message.'));
            }
            /** @var $dateModel Magento_Core_Model_Date */
            $dateModel = $this->_objectManager->get('Magento_Core_Model_Date');
            /** @var $history Magento_Rma_Model_Rma_Status_History */
            $history = $this->_objectManager->create('Magento_Rma_Model_Rma_Status_History');
            $history->setRmaEntityId((int)$rma->getId())
                ->setComment($comment)
                ->setIsVisibleOnFront($visible)
                ->setIsCustomerNotified($notify)
                ->setStatus($rma->getStatus())
                ->setCreatedAt($dateModel->gmtDate())
                ->setIsAdmin(1)
                ->save();

            if ($notify && $history) {
                $history->setRma($rma);
                $history->setStoreId($rma->getStoreId());
                $history->sendCommentEmail();
            }

            $this->loadLayout();
            $response = $this->getLayout()->getBlock('comments_history')->toHtml();
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot add the RMA history.'),
            );
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Generate RMA grid for ajax request from customer page
     */
    public function rmaCustomerAction()
    {
        $customerId = intval($this->getRequest()->getParam('id'));
        if ($customerId) {
            $this->getResponse()->setBody(
                $this
                    ->getLayout()
                    ->createBlock('Magento_Rma_Block_Adminhtml_Customer_Edit_Tab_Rma')
                    ->setCustomerId($customerId)
                    ->toHtml()
            );
        }
    }

    /**
     * Generate RMA grid for ajax request from order page
     */
    public function rmaOrderAction()
    {
        $orderId = intval($this->getRequest()->getParam('order_id'));
        $this->getResponse()->setBody(
            $this
                ->getLayout()
                ->createBlock('Magento_Rma_Block_Adminhtml_Order_View_Tab_Rma')
                ->setOrderId($orderId)
                ->toHtml()
        );
    }

    /**
     * Generate RMA items grid for ajax request from selecting product grid during RMA creation
     *
     * @throws Magento_Core_Exception
     */
    public function addProductGridAction()
    {
        try {
            $this->_initModel();
            $order = $this->_coreRegistry->registry('current_order');
            if (!$order) {
                throw new Magento_Core_Exception(__('Invalid order'));
            }
            $this->loadLayout();
            $response = $this->getLayout()->getBlock('add_product_grid')->toHtml();
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('Something went wrong retrieving the product list.')
            );
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Generate PDF form of RMA
     */
    public function printAction()
    {
        $rmaId = (int)$this->getRequest()->getParam('rma_id');
        if ($rmaId) {
            /** @var $rmaModel Magento_Rma_Model_Rma */
            $rmaModel = $this->_objectManager->create('Magento_Rma_Model_Rma')->load($rmaId);
            if ($rmaModel) {
                /** @var $dateModel Magento_Core_Model_Date */
                $dateModel = $this->_objectManager->get('Magento_Core_Model_Date');
                /** @var $pdfModel Magento_Rma_Model_Pdf_Rma */
                $pdfModel = $this->_objectManager->create('Magento_Rma_Model_Pdf_Rma');
                $pdf = $pdfModel->getPdf(array($rmaModel));
                $this->_prepareDownloadResponse(
                    'rma' . $dateModel->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            }
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Load user-defined attributes of RMA's item
     *
     * @throws Magento_Core_Exception
     */
    public function loadAttributesAction()
    {
        $response = false;
        $itemId = $this->getRequest()->getParam('item_id');

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('The wrong RMA was requested.'));
            }
            /** @var $rma_item Magento_Rma_Model_Item */
            $rma_item = $this->_objectManager->create('Magento_Rma_Model_Item');
            if ($itemId) {
                $rma_item->load($itemId);
                if (!$rma_item->getId()) {
                    throw new Magento_Core_Exception(__('The wrong RMA item was requested.'));
                }
                $this->_coreRegistry->register('current_rma_item', $rma_item);
            } else {
                throw new Magento_Core_Exception(__('The wrong RMA item was requested.'));
            }
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot display the item attributes.')
            );
        }

        $this->loadLayout();
        $block = $this
                ->getLayout()
                ->getBlock('magento_rma_edit_item')
                ->initForm();
        $block->getForm()->setHtmlIdPrefix('_rma' . $itemId);
        $response = $block->toHtml();

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Load user-defined attributes for new RMA's item
     */
    public function loadNewAttributesAction()
    {
        $response = false;
        $orderId = $this->getRequest()->getParam('order_id');
        $productId = $this->getRequest()->getParam('product_id');

        /** @var $rma_item Magento_Rma_Model_Item */
        $rma_item = $this->_objectManager->create('Magento_Rma_Model_Item');
        $this->_coreRegistry->register('current_rma_item', $rma_item);

        $this->loadLayout();
        $response = $this
            ->getLayout()
            ->getBlock('magento_rma_edit_item')
            ->setProductId(intval($productId))
            ->initForm()
            ->toHtml();

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }


    /**
     * Load new row of RMA's item for Split Line functionality
     *
     * @throws Magento_Core_Exception
     */
    public function loadSplitLineAction()
    {
        $response = false;
        $rmaId = $this->getRequest()->getParam('rma_id');
        $itemId = $this->getRequest()->getParam('item_id');

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('The wrong RMA was requested.'));
            }
            /** @var $rma_item Magento_Rma_Model_Item */
            $rma_item = $this->_objectManager->create('Magento_Rma_Model_Item');
            if ($itemId) {
                $rma_item->load($itemId);
                if (!$rma_item->getId()) {
                    throw new Magento_Core_Exception(__('The wrong RMA item was requested.'));
                }
                $this->_coreRegistry->register('current_rma_item', $rma_item);
            } else {
                throw new Magento_Core_Exception(__('The wrong RMA item was requested.'));
            }
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot display the item attributes.')
            );
        }

        $this->loadLayout();

        $response = $this
            ->getLayout()
            ->getBlock('magento_rma_edit_items_grid')
            ->setItemFilter($itemId)
            ->setAllFieldsEditable()
            ->toHtml();

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }


    /**
     * Check the permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rma::magento_rma');
    }

    /**
     * Shows bundle items on rma create
     *
     * @throws Magento_Core_Exception
     */
    public function showBundleItemsAction()
    {
        $response   = false;
        $orderId    = $this->getRequest()->getParam('order_id');
        $itemId     = $this->getRequest()->getParam('item_id');

        try {
            if ($orderId && $itemId) {
                /** @var $item Magento_Rma_Model_Resource_Item */
                $item = $this->_objectManager->create('Magento_Rma_Model_Resource_Item');
                /** @var $items Magento_Sales_Model_Resource_Order_Item_Collection */
                $items = $item->getOrderItems($orderId, $itemId);
                if (empty($items)) {
                    throw new Magento_Core_Exception(__('No items for bundle product'));
                }
            } else {
                throw new Magento_Core_Exception(__('The wrong order ID or item ID was requested.'));
            }

            $this->_coreRegistry->register('current_rma_bundle_item', $items);
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot display the item attributes.')
            );
        }

        $this->loadLayout();
        $response = $this->getLayout()
            ->getBlock('magento_rma_bundle')
            ->toHtml()
        ;

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Action for view full sized item atttribute image
     */
    public function viewfileAction()
    {
        $file   = null;
        $plain  = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file   = $this->_objectManager->get('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('file'));
        } else if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file   = $this->_objectManager->get('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('image'));
            $plain  = true;
        } else {
            return $this->norouteAction();
        }
        /** @var $dirModel Magento_Core_Model_Dir */
        $dirModel = $this->_objectManager->get('Magento_Core_Model_Dir');
        $path = $dirModel->getDir(Magento_Core_Model_Dir::MEDIA) . DS . 'rma_item';

        $ioFile = new Magento_Io_File();
        $ioFile->open(array('path' => $path));
        $fileName   = $ioFile->getCleanPath($path . $file);
        $path       = $ioFile->getCleanPath($path);

        if (!$ioFile->fileExists($fileName) || strpos($fileName, $path) !== 0) {
            return $this->norouteAction();
        }

        if ($plain) {
            $contentType = $this->_getPlainImageMimeType(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)));
            $ioFile->streamOpen($fileName, 'r');
            $contentLength = $ioFile->streamStat('size');
            $contentModify = $ioFile->streamStat('mtime');

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify))
                ->clearBody();
            $this->getResponse()->sendHeaders();

            while (false !== ($buffer = $ioFile->streamRead())) {
                echo $buffer;
            }
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_prepareDownloadResponse($name, array(
                'type'  => 'filename',
                'value' => $fileName
            ));
        }

        exit();
    }

    /**
     * Retrieve image MIME type by its extension
     *
     * @param string $extension
     * @return string
     */
    protected function _getPlainImageMimeType($extension)
    {
        $mimeTypeMap = array(
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'png' => 'image/png'
        );
        $contentType = 'application/octet-stream';
        if (isset($mimeTypeMap[$extension])) {
            $contentType = $mimeTypeMap[$extension];
        }
        return $contentType;
    }

    /**
     * Shows available shipping methods
     *
     * @throws Magento_Core_Exception
     */
    public function showShippingMethodsAction()
    {
        $response   = false;

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('This is the wrong RMA ID.'));
            }

        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot display the available shipping methods.')
            );
        }

        $this->loadLayout();
        $response = $this->getLayout()
            ->getBlock('magento_rma_shipping_available')
            ->toHtml()
        ;

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Shows available shipping methods
     *
     * @return Zend_Controller_Response_Abstract
     * @throws Magento_Core_Exception
     */
    public function pslAction()
    {
        $data       = $this->getRequest()->getParam('data');
        $response   = false;

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new Magento_Core_Exception(__('This is the wrong RMA ID.'));
            }

        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot display the available shipping methods.')
            );
        }

        if ($data) {
            return $this->getResponse()
                ->setBody($this->_getConfigDataJson($model)
            );
        }

        $this->loadLayout();
        $response = $this->getLayout()
            ->getBlock('magento_rma_shipment_packaging')
            ->toHtml()
        ;

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Configuration for popup window for packaging
     *
     * @param Magento_Rma_Model_Rma $model
     * @return string
     */
    protected function _getConfigDataJson($model)
    {
        $urlParams      = array();
        $itemsQty       = array();
        $itemsPrice     = array();
        $itemsName      = array();
        $itemsWeight    = array();
        $itemsProductId = array();

        $urlParams['id']    = $model->getId();
        $items              = $model->getShippingMethods(true);

        $createLabelUrl = $this->getUrl('*/*/saveShipping', $urlParams);
        $itemsGridUrl   = $this->getUrl('*/*/getShippingItemsGrid', $urlParams);
        $thisPage       = $this->getUrl('*/*/edit', $urlParams);

        $code    = $this->getRequest()->getParam('method');
        $carrier = $this->_objectManager->get('Magento_Rma_Helper_Data')->getCarrier($code, $model->getStoreId());
        if ($carrier) {
            $getCustomizableContainers =  $carrier->getCustomizableContainerTypes();
        }

        foreach ($items as $item) {
            $itemsQty[$item->getItemId()]           = $item->getQty();
            $itemsPrice[$item->getItemId()]         = $item->getPrice();
            $itemsName[$item->getItemId()]          = $item->getName();
            $itemsWeight[$item->getItemId()]        = $item->getWeight();
            $itemsProductId[$item->getItemId()]     = $item->getProductId();
            $itemsOrderItemId[$item->getItemId()]   = $item->getItemId();
        }

        $shippingInformation = $this->getLayout()
            ->createBlock('Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Information')
            ->setIndex($this->getRequest()->getParam('index'))
            ->toHtml();

        $data = array(
            'createLabelUrl'            => $createLabelUrl,
            'itemsGridUrl'              => $itemsGridUrl,
            'errorQtyOverLimit'         => __("A quantity you're trying to add is higher than the number of products we shipped."),
            'titleDisabledSaveBtn'      => __('Products should be added to package(s)'),
            'validationErrorMsg'        => __('You entered an invalid value.'),
            'shipmentItemsQty'          => $itemsQty,
            'shipmentItemsPrice'        => $itemsPrice,
            'shipmentItemsName'         => $itemsName,
            'shipmentItemsWeight'       => $itemsWeight,
            'shipmentItemsProductId'    => $itemsProductId,
            'shipmentItemsOrderItemId'  => $itemsOrderItemId,

            'shippingInformation'       => $shippingInformation,
            'thisPage'                  => $thisPage,
            'customizable'              => $getCustomizableContainers
        );

        return $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($data);
    }

    /**
     * Return grid with shipping items for Ajax request
     */
    public function getShippingItemsGridAction()
    {
        $this->_initModel();
        $response = $this-> _initAction()
                ->getLayout()
                ->getBlock('magento_rma_getshippingitemsgrid')
                ->toHtml()
        ;

        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @throws Magento_Core_Exception
     */
    public function saveShippingAction()
    {
        $responseAjax = new Magento_Object();

        try {
            $model = $this->_initModel();
            if ($model) {
                if ($this->_createShippingLabel($model)) {
                    $this->_getSession()
                        ->addSuccess(__('You created a shipping label.'));
                    $responseAjax->setOk(true);
                }
                $this->_backendSession->getCommentText(true);
            } else {
                $this->_forward('noRoute');
                return;
            }
        } catch (Magento_Core_Exception $e) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                $responseAjax->setError(true);
                $responseAjax->setMessage(__('Something went wrong creating a shipping label.'));
        }
        $this->getResponse()->setBody($responseAjax->toJson());
    }

    /**
     * Create shipping label action for specific shipment
     *
     * @throws Magento_Core_Exception
     */
    public function createLabelAction()
    {
        $response = new Magento_Object();
        try {
            $shipment = $this->_initShipment();
            if ($this->_createShippingLabel($shipment)) {
                $shipment->save();
                $this->_getSession()->addSuccess(__('You created a shipping label.'));
                $response->setOk(true);
            }
        } catch (Magento_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $response->setError(true);
            $response->setMessage(__('Something went wrong creating a shipping label.'));
        }

        $this->getResponse()->setBody($response->toJson());
        return;
    }

    /**
     * Create shipping label for specific shipment with validation.
     *
     * @param Magento_Rma_Model_Rma $model
     * @return bool
     * @throws Magento_Core_Exception
     */
    protected function _createShippingLabel(Magento_Rma_Model_Rma $model)
    {
        $data = $this->getRequest()->getPost();
        if ($model && isset($data['packages']) && !empty($data['packages'])) {
            /** @var $shippingModel Magento_Rma_Model_Shipping */
            $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
            /** @var $shipment Magento_Rma_Model_Shipping */
            $shipment = $shippingModel->getShippingLabelByRma($model);

            $carrier = $this->_objectManager->get('Magento_Rma_Helper_Data')->getCarrier($data['code'], $model->getStoreId());
            if (!$carrier->isShippingLabelsAvailable()) {
                return false;
            }
            $shipment->setPackages($data['packages']);
            $shipment->setCode($data['code']);

            list($carrierCode, $methodCode) = explode('_', $data['code'], 2);
            $shipment->setCarrierCode($carrierCode);
            $shipment->setMethodCode($data['code']);

            $shipment->setCarrierTitle($data['carrier_title']);
            $shipment->setMethodTitle($data['method_title']);
            $shipment->setPrice($data['price']);
            $shipment->setRma($model);
            $shipment->setIncrementId($model->getIncrementId());
            $weight = 0;
            foreach ($data['packages'] as $package) {
                $weight += $package['params']['weight'];
            }
            $shipment->setWeight($weight);

            $response = $shipment->requestToShipment();

            if (!$response->hasErrors() && $response->hasInfo()) {
                $labelsContent      = array();
                $trackingNumbers    = array();
                $info = $response->getInfo();

                foreach ($info as $inf) {
                    if (!empty($inf['tracking_number']) && !empty($inf['label_content'])) {
                        $labelsContent[]    = $inf['label_content'];
                        $trackingNumbers[]  = $inf['tracking_number'];
                    }
                }
                $outputPdf = $this->_combineLabelsPdf($labelsContent);
                $shipment->setPackages(serialize($data['packages']));
                $shipment->setShippingLabel($outputPdf->render());
                $shipment->setIsAdmin(Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL);
                $shipment->setRmaEntityId($model->getId());
                $shipment->save();

                $carrierCode = $carrier->getCarrierCode();
                $carrierTitle = $this->_objectManager->get('Magento_Core_Model_Store_Config')->getConfig('carriers/'.$carrierCode.'/title', $shipment->getStoreId());
                if ($trackingNumbers) {
                    /** @var $shippingResource Magento_Rma_Model_Resource_Shipping */
                    $shippingResource = $this->_objectManager->create('Magento_Rma_Model_Resource_Shipping');
                    $shippingResource->deleteTrackingNumbers($model);
                    foreach ($trackingNumbers as $trackingNumber) {
                        /** @var $shippingModel Magento_Rma_Model_Shipping */
                        $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
                        $shippingModel->setTrackNumber($trackingNumber)
                            ->setCarrierCode($carrierCode)
                            ->setCarrierTitle($carrierTitle)
                            ->setRmaEntityId($model->getId())
                            ->setIsAdmin(Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER)
                            ->save();
                    }
                }
                return true;
            } else {
                throw new Magento_Core_Exception($response->getErrors());
            }
        }
        return false;
    }

    /**
     * Print label for one specific shipment
     *
     * @return Magento_Adminhtml_Controller_Action
     * @throws Magento_Core_Exception
     */
    public function printLabelAction()
    {
        try {
            $model = $this->_initModel();
            /** @var $shippingModel Magento_Rma_Model_Shipping */
            $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
            $labelContent = $shippingModel->getShippingLabelByRma($model)->getShippingLabel();
            if ($labelContent) {
                $pdfContent = null;
                if (stripos($labelContent, '%PDF-') !== false) {
                    $pdfContent = $labelContent;
                } else {
                    $pdf = new Zend_Pdf();
                    $page = $this->_createPdfPageFromImageString($labelContent);
                    if (!$page) {
                        $this->_getSession()->addError(__("We don't recognize or support the file extension in shipment %1.", $model->getIncrementId()));
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_prepareDownloadResponse(
                    'ShippingLabel(' . $model->getIncrementId() . ').pdf',
                    $pdfContent,
                    'application/pdf'
                );
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_getSession()
                ->addError(__('Something went wrong creating a shipping label.'));
       }
        $this->_redirect('*/*/edit', array(
            'id' => $this->getRequest()->getParam('id')
        ));
    }

    /**
     * Create pdf document with information about packages
     */
    public function printPackageAction()
    {
        $model = $this->_initModel();
        /** @var $shippingModel Magento_Rma_Model_Shipping */
        $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
        $shipment = $shippingModel->getShippingLabelByRma($model);

        if ($shipment) {
            /** @var $orderPdf Magento_Sales_Model_Order_Pdf_Shipment_Packaging */
            $orderPdf = $this->_objectManager->create('Magento_Sales_Model_Order_Pdf_Shipment_Packaging');
            /** @var $block Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod */
            $block = $this->_layout->getBlockSingleton(
                'Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingmethod'
            );
            $orderPdf->setPackageShippingBlock($block);
            $pdf = $orderPdf->getPdf($shipment);
            /** @var $dateModel Magento_Core_Model_Date */
            $dateModel = $this->_objectManager->get('Magento_Core_Model_Date');
            $this->_prepareDownloadResponse(
                'packingslip' . $dateModel->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(),
                'application/pdf'
            );
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Create Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return Zend_Pdf_Page|bool
     */
    protected function _createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
                     . uniqid(mt_rand()) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return $page;
    }

    /**
     * Combine Labels Pdf
     *
     * @param array $labelsContent
     * @return Zend_Pdf
     */
    protected function _combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                $page = $this->_createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }
            }
        }
        return $outputPdf;
    }

    /**
     * Add new tracking number action
     *
     * @throws Magento_Core_Exception
     */
    public function addTrackAction()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number  = $this->getRequest()->getPost('number');
            $title  = $this->getRequest()->getPost('title');
            if (empty($carrier)) {
                throw new Magento_Core_Exception(__('Please specify a carrier.'));
            }
            if (empty($number)) {
                throw new Magento_Core_Exception(__('You need to enter a tracking number.'));
            }

            $model = $this->_initModel();
            if ($model->getId()) {
                /** @var $shippingModel Magento_Rma_Model_Shipping */
                $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
                $shippingModel
                    ->setTrackNumber($number)
                    ->setCarrierCode($carrier)
                    ->setCarrierTitle($title)
                    ->setRmaEntityId($model->getId())
                    ->setIsAdmin(Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_TRACKING_NUMBER)
                    ->save()
                ;

                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
            } else {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot initialize an RMA to add a tracking number.'),
                );
            }
        } catch (Magento_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot add a message.'),
            );
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    /**
     * Remove tracking number from shipment
     */
    public function removeTrackAction()
    {
        $trackId    = $this->getRequest()->getParam('track_id');
        /** @var $shippingModel Magento_Rma_Model_Shipping */
        $shippingModel = $this->_objectManager->create('Magento_Rma_Model_Shipping');
        $shippingModel->load($trackId);
        if ($shippingModel->getId()) {
            try {
                $model = $this->_initModel();
                if ($model->getId()) {
                    $shippingModel->delete();

                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipment_tracking')->toHtml();
                } else {
                    $response = array(
                        'error'     => true,
                        'message'   => __('We cannot initialize an RMA to delete a tracking number.'),
                    );
                }
            } catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot delete the tracking number.'),
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => __('We cannot load track with retrieving identifier.'),
            );
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }
}
