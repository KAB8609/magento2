<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Manage Newsletter Template Controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Newsletter;

class Template extends \Magento\Backend\App\Action
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
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Magento_Newsletter::template');
    }

    /**
     * Set title of page
     *
     * @return \Magento\Adminhtml\Controller\Newsletter\Template
     */
    protected function _setTitle()
    {
        return $this->_title->add(__('Newsletter Templates'));
    }

    /**
     * View Templates list
     *
     */
    public function indexAction()
    {
        $this->_setTitle();

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_Newsletter::newsletter_template');
        $this->_addBreadcrumb(__('Newsletter Templates'), __('Newsletter Templates'));
        $this->_addContent($this->_layoutServices->getLayout()->createBlock('Magento\Adminhtml\Block\Newsletter\Template', 'template'));
        $this->_layoutServices->renderLayout();
    }

    /**
     * JSON Grid Action
     *
     */
    public function gridAction()
    {
        $this->_layoutServices->loadLayout();
        $grid = $this->_layoutServices->getLayout()->createBlock('Magento\Adminhtml\Block\Newsletter\Template\Grid')
            ->toHtml();
        $this->getResponse()->setBody($grid);
    }

    /**
     * Create new Newsletter Template
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Newsletter Template
     *
     */
    public function editAction()
    {
        $this->_setTitle();

        $model = $this->_objectManager->create('Magento\Newsletter\Model\Template');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('_current_template', $model);

        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_Newsletter::newsletter_template');

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Template');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Template');
            $breadcrumbLabel = __('Create Newsletter Template');
        }

        $this->_title->add($model->getId() ? $model->getTemplateCode() : __('New Template'));

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        $values = $this->_getSession()->getData('newsletter_template_form_data', true);
        if ($values) {
            $model->addData($values);
        }

        $editBlock = $this->_layoutServices->getLayout()->getBlock('template_edit');
        if ($editBlock) {
            $editBlock->setEditMode($model->getId() > 0);
        }

        $this->_layoutServices->renderLayout();
    }

    /**
     * Drop Newsletter Template
     *
     */
    public function dropAction()
    {
        $this->_layoutServices->loadLayout('newsletter_template_preview');
        $this->_layoutServices->renderLayout();
    }

    /**
     * Save Newsletter Template
     *
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('adminhtml/newsletter_template'));
        }
        $template = $this->_objectManager->create('Magento\Newsletter\Model\Template');

        $id = (int)$request->getParam('id');
        if ($id) {
            $template->load($id);
        }

        try {
            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setTemplateStyles($request->getParam('styles'))
                ->setModifiedAt($this->_objectManager->get('Magento\Core\Model\Date')->gmtDate());

            if (!$template->getId()) {
                $template->setTemplateType(\Magento\Newsletter\Model\Template::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(\Magento\Newsletter\Model\Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }

            $template->save();

            $this->_getSession()->addSuccess(__('The newsletter template has been saved.'));
            $this->_getSession()->setFormData(false);

            $this->_redirect('adminhtml/*');
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('newsletter_template_form_data',
                $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e,
                __('An error occurred while saving this template.')
            );
            $this->_getSession()->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        }

        $this->_forward('new');
    }

    /**
     * Delete newsletter Template
     *
     */
    public function deleteAction()
    {
        $template = $this->_objectManager->create('Magento\Newsletter\Model\Template')
            ->load($this->getRequest()->getParam('id'));
        if ($template->getId()) {
            try {
                $template->delete();
                $this->_getSession()->addSuccess(__('The newsletter template has been deleted.'));
                $this->_getSession()->setFormData(false);
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException($e,
                    __('An error occurred while deleting this template.')
                );
            }
        }
        $this->_redirect('adminhtml/*');
    }

    /**
     * Preview Newsletter template
     *
     */
    public function previewAction()
    {
        $this->_setTitle();
        $this->_layoutServices->loadLayout();

        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noroute');
            return $this;
        }

        // set default value for selected store
        $data['preview_store_id'] = $this->_objectManager->get('Magento\Core\Model\StoreManager')
            ->getDefaultStoreView()->getId();

        $this->_layoutServices->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->_layoutServices->renderLayout();
    }
}
