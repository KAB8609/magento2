<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Controller\Adminhtml;

/**
 * Controller for integrations management.
 */
class Integration extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Integrations grid.
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_Integration::system_integrations');
        $this->_addBreadcrumb(
            __('Integrations'),
            __('Integrations')
        );
        $this->_title(__('Integrations'));
        $this->renderLayout();
    }

    /**
     * AJAX integrations grid.
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
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

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $integrationId = (int)$this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\Integration\Model\Integration');
        if ($integrationId) {
            $model->load($integrationId);
        }

        if (!$model->getId() && $integrationId) {
            $this->_getSession()
                ->addError(__('This integration no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}