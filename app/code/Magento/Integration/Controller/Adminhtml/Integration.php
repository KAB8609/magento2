<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;
use Magento\Integration\Exception as IntegrationException;
use Magento\Integration\Service\OauthV1Interface as IntegrationOauthService;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * Controller for integrations management.
 */
class Integration extends Action
{
    /** Param Key for extracting integration id from Request */
    const PARAM_INTEGRATION_ID = 'id';

    /** Reauthorize flag is used to distinguish activation from reauthorization */
    const PARAM_REAUTHORIZE = 'reauthorize';

    /** Param key to pass consumer ID */
    const PARAM_CONSUMER_ID = 'consumer_id';

    /** Registry key for current integration ID */
    const REGISTRY_KEY_CURRENT_INTEGRATION = 'current_integration';

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /** @var \Magento\Logger */
    protected $_logger;

    /** @var \Magento\Integration\Service\IntegrationV1Interface */
    private $_integrationService;

    /** @var IntegrationOauthService */
    protected $_oauthService;

    /** @var \Magento\Core\Helper\Data */
    protected $_coreHelper;

    /** @var \Magento\Integration\Helper\Data */
    protected $_integrationData;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Logger $logger
     * @param IntegrationOauthService $oauthService
     * @param \Magento\Integration\Service\IntegrationV1Interface $integrationService
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Integration\Helper\Data $integrationData
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Logger $logger,
        \Magento\Integration\Service\IntegrationV1Interface $integrationService,
        IntegrationOauthService $oauthService,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Integration\Helper\Data $integrationData
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_logger = $logger;
        $this->_integrationService = $integrationService;
        $this->_oauthService = $oauthService;
        $this->_coreHelper = $coreHelper;
        $this->_integrationData = $integrationData;
        parent::__construct($context);
    }

    /**
     * Integrations grid.
     */
    public function indexAction()
    {
        if ($this->_request->has(self::PARAM_CONSUMER_ID)) {
            // We've got here by redirecting from activate action via token exchange
            $consumerId = $this->_request->getParam(self::PARAM_CONSUMER_ID);
            $isReauthorize = (bool)$this->_request->getParam(self::PARAM_REAUTHORIZE, 0);

            if (!is_null($consumerId)) {
                try {
                    $integration = $this->_integrationService->findByConsumerId($consumerId);
                    $this->_setActivationInProcessMsg($isReauthorize, $integration->getName());
                } catch (\Magento\Integration\Exception $e) {
                    // Do nothing - there's no such integration
                }
            }
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(__('Integrations'), __('Integrations'));
        $this->_title->add(__('Integrations'));
        $this->_view->renderLayout();
    }

    /**
     * AJAX integrations grid.
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Check ACL.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Integration::integrations');
    }

    /**
     * New integration action.
     */
    public function newAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(__('New Integration'), __('New Integration'));
        $this->_title->add(__('New Integration'));
        /** Try to recover integration data from session if it was added during previous request which failed. */
        $restoredIntegration = $this->_getSession()->getIntegrationData();
        if ($restoredIntegration) {
            $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $restoredIntegration);
            $this->_getSession()->setIntegrationData(array());
        }
        $this->_view->renderLayout();
    }

    /**
     * Edit integration action.
     */
    public function editAction()
    {
        /** Try to recover integration data from session if it was added during previous request which failed. */
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            try {
                $integrationData = $this->_integrationService->get($integrationId)->getData();
                $originalName = $integrationData[Info::DATA_NAME];
            } catch (IntegrationException $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_logger->logException($e);
                $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                $this->_redirect('*/*');
                return;
            }
            $restoredIntegration = $this->_getSession()->getIntegrationData();
            if (isset($restoredIntegration[Info::DATA_ID]) && $integrationId == $restoredIntegration[Info::DATA_ID]) {
                $integrationData = array_merge($integrationData, $restoredIntegration);
            }
        } else {
            $this->_getSession()->addError(__('Integration ID is not specified or is invalid.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
        $this->_view->loadLayout();
        $this->_getSession()->setIntegrationData(array());
        $this->_setActiveMenu('Magento_Integration::system_integrations');

        if ($this->_integrationData->isConfigType($integrationData)) {
            $title = __('View "%1" Integration', $originalName);
        } else {
            $title = __('Edit "%1" Integration', $originalName);
        }

        $this->_addBreadcrumb($title, $title);
        $this->_title->add($title);
        $this->_view->renderLayout();
    }

    /**
     * Save integration action.
     *
     * TODO: Fix cyclomatic complexity.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveAction()
    {
        /** @var array $integrationData */
        $integrationData = array();
        try {
            $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            if ($integrationId) {
                try {
                    $integrationData = $this->_integrationService->get($integrationId)->getData();
                } catch (IntegrationException $e) {
                    $this->_getSession()->addError($e->getMessage());
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                    $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                    $this->_redirect('*/*');
                    return;
                }
            }
            /** @var array $data */
            $data = $this->getRequest()->getPost();
            if (!empty($data)) {
                // TODO: Move out work with API permissions to Web API module
                if (!isset($data['resource'])) {
                    $integrationData['resource'] = array();
                }
                $integrationData = array_merge($integrationData, $data);
                if (!isset($integrationData[Info::DATA_ID])) {
                    $integration = $this->_integrationService->create($integrationData);
                } else {
                    $integration = $this->_integrationService->update($integrationData);
                }
                if (!$this->getRequest()->isXmlHttpRequest()) {
                    $this->_getSession()
                        ->addSuccess(__('The integration \'%1\' has been saved.', $integration->getName()));
                }
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $isTokenExchange = ($integration->getEndpoint() && $integration->getIdentityLinkUrl()) ? '1' : '0';
                    $this->getResponse()->setBody(
                        $this->_coreHelper->jsonEncode(
                            ['integrationId' => $integration->getId(), 'isTokenExchange' => $isTokenExchange]
                        )
                    );
                } else {
                    $this->_redirect('*/*/');
                }
            } else {
                $this->_getSession()->addError(__('The integration was not saved.'));
            }
        } catch (IntegrationException $e) {
            $this->_getSession()->addError($e->getMessage())->setIntegrationData($integrationData);
            $this->_redirectOnSaveError();
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectOnSaveError();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectOnSaveError();
        }
    }

    /**
     * Show permissions popup.
     */
    public function permissionsDialogAction()
    {
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            try {
                $integrationData = $this->_integrationService->get($integrationId)->getData();
                $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
            } catch (IntegrationException $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->_logger->logException($e);
                $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
                $this->_redirect('*/*');
                return;
            }
        } else {
            $this->_getSession()->addError(__('Integration ID is not specified or is invalid.'));
            $this->_redirect('*/*/');
            return;
        }

        /** Add handles of the tabs which are defined in other modules */
        $handleNodes = $this->_view->getLayout()->getUpdate()->getFileLayoutUpdatesXml()
            ->xpath('//referenceBlock[@name="integration.activate.permissions.tabs"]/../@id');
        $handles = array();
        if (is_array($handleNodes)) {
            foreach ($handleNodes as $node) {
                $handles[] = (string)$node;
            }
        }
        $this->_view->loadLayout($handles);
        $this->_view->renderLayout();
    }

    /**
     * Delete the integration.
     */
    public function deleteAction()
    {
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        try {
            if ($integrationId) {
                $integrationData = $this->_integrationService->get($integrationId);
                if ($this->_integrationData->isConfigType($integrationData)) {
                    $this->_getSession()->addError(
                        __("Uninstall the extension to remove integration '%1'.", $integrationData[Info::DATA_NAME])
                    );
                    $this->_redirect('*/*/');
                    return;
                }
                $integrationData = $this->_integrationService->delete($integrationId);
                if (!$integrationData[Info::DATA_ID]) {
                    $this->_getSession()->addError(__('This integration no longer exists.'));
                } else {
                    //Integration deleted successfully, now safe to delete the associated consumer data
                    if (isset($integrationData[Info::DATA_CONSUMER_ID])) {
                        $this->_oauthService->deleteConsumer($integrationData[Info::DATA_CONSUMER_ID]);
                    }
                    $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
                    $this->_getSession()
                        ->addSuccess(__("The integration '%1' has been deleted.", $integrationData[Info::DATA_NAME]));
                }
            } else {
                $this->_getSession()->addError(__('Integration ID is not specified or is invalid.'));
            }
        } catch (\Magento\Integration\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Show tokens popup for simple tokens
     */
    public function tokensDialogAction()
    {
        try {
            $integrationId = $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            $integration = $this->_integrationService->get($integrationId);
            $clearExistingToken = (bool)$this->getRequest()->getParam(self::PARAM_REAUTHORIZE, 0);
            if ($this->_oauthService->createAccessToken($integration->getConsumerId(), $clearExistingToken)) {
                $integration->setStatus(IntegrationModel::STATUS_ACTIVE)->save();
            }
            $this->_registry->register(
                self::REGISTRY_KEY_CURRENT_INTEGRATION,
                $this->_integrationService->get($integrationId)->getData()
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
            return;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
            $this->_redirect('*/*');
            return;
        }
        $this->_view->loadLayout(false);
        //This cannot precede loadlayout(false) else the messages will be removed
        $this->_setActivationSuccessMsg($clearExistingToken, $integration->getName());
        $this->_view->renderLayout();
    }

    /**
     * Post consumer credentials for Oauth integration.
     */
    public function tokensExchangeAction()
    {
        try {
            $integrationId = $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
            $isReauthorize = (bool)$this->getRequest()->getParam(self::PARAM_REAUTHORIZE, 0);
            $integration = $this->_integrationService->get($integrationId);

            if ($isReauthorize) {
                /** Remove existing token associated with consumer before issuing a new one. */
                $areTokensCleared = $this->_oauthService->deleteToken($integration->getConsumerId());
            }

            if (!isset($areTokensCleared) || $areTokensCleared) {
                $integration->setStatus(IntegrationModel::STATUS_INACTIVE)->save();
            } else {
                $this->_setActivationFailedMsg($isReauthorize, $integration->getName());
                $this->_redirect('*/*');
                return;
            }
            //Integration chooses to use Oauth for token exchange
            $this->_oauthService->postToConsumer($integration->getConsumerId(), $integration->getEndpoint());
            /** Generate JS popup content */
            $this->_view->loadLayout(false);
            $this->_view->renderLayout();
            $popupContent = $this->_response->getBody();
            /** Initialize response body */
            $result = [
                IntegrationModel::IDENTITY_LINK_URL => $integration->getIdentityLinkUrl(),
                IntegrationModel::CONSUMER_ID => $integration->getConsumerId(),
                'is_reauthorize' => $isReauthorize,
                'popup_content' => $popupContent
            ];
            $this->getResponse()->setBody($this->_coreHelper->jsonEncode($result));
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
            return;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            $this->_getSession()->addError(__('Internal error. Check exception log for details.'));
            $this->_redirect('*/*');
            return;
        }
    }

    /**
     * Close window after callback has succeeded
     */
    public function loginSuccessCallbackAction()
    {
        $this->getResponse()->setBody('<script type="text/javascript">setTimeout("self.close()",1000);</script>');
    }

    /**
     * Redirect merchant to 'Edit integration' or 'New integration' if error happened during integration save.
     */
    protected function _redirectOnSaveError()
    {
        $integrationId = $this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);
        if ($integrationId) {
            $this->_redirect('*/*/edit', array('id' => $integrationId));
        } else {
            $this->_redirect('*/*/new');
        }
    }

    /**
     * Don't actually redirect if we've got AJAX request - return redirect URL instead.
     *
     * @param string $path
     * @param array $arguments
     * @return $this|\Magento\Backend\App\AbstractAction
     */
    protected function _redirect($path, $arguments = array())
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setBody(
                $this->_coreHelper->jsonEncode(['_redirect' => $this->getUrl($path, $arguments)])
            );
            return $this;
        } else {
            return parent::_redirect($path, $arguments);
        }
    }

    /**
     * Set success message based on Integration activation or re-authorization.
     *
     * @param boolean $isReauthorize Is a re-authorization flow
     * @param string $integrationName Integration name
     */
    protected function _setActivationSuccessMsg($isReauthorize, $integrationName)
    {
        $successMsg = $isReauthorize ? __("The integration '%1' has been re-authorized.", $integrationName)
                                     : __("The integration '%1' has been activated.", $integrationName);
        $this->_getSession()->addSuccess($successMsg);
    }

    /**
     * Let the admin know that activation was failed.
     *
     * @param bool   $isReauthorize
     * @param string $integrationName
     */
    protected function _setActivationFailedMsg($isReauthorize, $integrationName)
    {
        $msg = $isReauthorize ? __("Integration '%1' re-authorization has been failed.", $integrationName)
                              : __("Integration '%1' activation has been failed.", $integrationName);
        $this->_getSession()->addError($msg);
    }

    /**
     * Let the admin know that integration has been sent for activation and token exchange is in process.
     *
     * @param bool   $isReauthorize
     * @param string $integrationName
     */
    protected function _setActivationInProcessMsg($isReauthorize, $integrationName)
    {
        $msg = $isReauthorize ? __("Integration '%1' has been sent for re-authorization.", $integrationName)
                              : __("Integration '%1' has been sent for activation.", $integrationName);
        $this->_getSession()->addNotice($msg);
    }
}
