<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom Variables admin controller
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\Adminhtml\System;

class Variable extends \Magento\Backend\App\Action
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
        $this->_title = $title;
        parent::__construct($context);
    }

    /**
     * Initialize Layout and set breadcrumbs
     *
     * @return \Magento\Backend\Controller\Adminhtml\System\Variable
     */
    protected function _initLayout()
    {
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_Adminhtml::system_variable')
            ->_addBreadcrumb(__('Custom Variables'), __('Custom Variables'));
        return $this;
    }

    /**
     * Initialize Variable object
     *
     * @return \Magento\Core\Model\Variable
     */
    protected function _initVariable()
    {
        $this->_title->add(__('Custom Variables'));

        $variableId = $this->getRequest()->getParam('variable_id', null);
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        /* @var $emailVariable \Magento\Core\Model\Variable */
        $variable = $this->_objectManager->create('Magento\Core\Model\Variable');
        if ($variableId) {
            $variable->setStoreId($storeId)
                ->load($variableId);
        }
        $this->_coreRegistry->register('current_variable', $variable);
        return $variable;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title->add(__('Custom Variables'));

        $this->_initLayout();
        $this->_layoutServices->renderLayout();
    }

    /**
     * New Action (forward to edit action)
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
        $variable = $this->_initVariable();

        $this->_title->add($variable->getId() ? $variable->getCode() : __('New Custom Variable'));

        $this->_initLayout()
            ->_addContent(
                $this->_layoutServices->getLayout()->createBlock('Magento\Backend\Block\System\Variable\Edit')
            )
            ->_addJs($this->_layoutServices->getLayout()->createBlock('Magento\Core\Block\Template', '', array(
                'data' => array('template' => 'Magento_Backend::system/variable/js.phtml')
            )));
        $this->_layoutServices->renderLayout();
    }

    /**
     * Validate Action
     */
    public function validateAction()
    {
        $response = new \Magento\Object(array('error' => false));
        $variable = $this->_initVariable();
        $variable->addData($this->getRequest()->getPost('variable'));
        $result = $variable->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_layoutServices->getLayout()->initMessages('Magento\Adminhtml\Model\Session');
            $response->setError(true);
            $response->setMessage($this->_layoutServices->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save Action
     */
    public function saveAction()
    {
        $variable = $this->_initVariable();
        $data = $this->getRequest()->getPost('variable');
        $back = $this->getRequest()->getParam('back', false);
        if ($data) {
            $data['variable_id'] = $variable->getId();
            $variable->setData($data);
            try {
                $variable->save();
                $this->_getSession()->addSuccess(
                    __('You saved the custom variable.')
                );
                if ($back) {
                    $this->_redirect('adminhtml/*/edit', array('_current' => true, 'variable_id' => $variable->getId()));
                } else {
                    $this->_redirect('adminhtml/*/', array());
                }
                return;
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current' => true, ));
                return;
            }
        }
        $this->_redirect('adminhtml/*/', array());
        return;
    }

    /**
     * Delete Action
     */
    public function deleteAction()
    {
        $variable = $this->_initVariable();
        if ($variable->getId()) {
            try {
                $variable->delete();
                $this->_getSession()->addSuccess(
                    __('You deleted the customer.')
                );
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current' => true, ));
                return;
            }
        }
        $this->_redirect('adminhtml/*/', array());
        return;
    }

    /**
     * WYSIWYG Plugin Action
     */
    public function wysiwygPluginAction()
    {
        $customVariables = $this->_objectManager->create('Magento\Core\Model\Variable')->getVariablesOptionArray(true);
        $storeContactVariabls = $this->_objectManager->create('Magento\Core\Model\Source\Email\Variables')->toOptionArray(true);
        $variables = array($storeContactVariabls, $customVariables);
        $this->getResponse()->setBody(\Zend_Json::encode($variables));
    }

    /**
     * Check current user permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::variable');
    }
}
