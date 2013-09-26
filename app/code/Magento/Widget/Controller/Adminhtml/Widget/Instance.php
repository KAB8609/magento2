<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Manage Widgets Instance Controller
 */
class Magento_Widget_Controller_Adminhtml_Widget_Instance extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Widget_Model_Widget_InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Widget_Model_Widget_InstanceFactory $widgetFactory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Widget_Model_Widget_InstanceFactory $widgetFactory,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_widgetFactory = $widgetFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Magento_Widget_Controller_Adminhtml_Widget_Instance
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Widget::cms_widget_instance')
            ->_addBreadcrumb(__('CMS'),
                __('CMS'))
            ->_addBreadcrumb(__('Manage Widget Instances'),
                __('Manage Widget Instances'));
        return $this;
    }

    /**
     * Init widget instance object and set it to registry
     *
     * @return Magento_Widget_Model_Widget_Instance|boolean
     */
    protected function _initWidgetInstance()
    {
        $this->_title(__('Frontend Apps'));

        /** @var $widgetInstance Magento_Widget_Model_Widget_Instance */
        $widgetInstance = $this->_widgetFactory->create();

        $instanceId = $this->getRequest()->getParam('instance_id', null);
        $type = $this->getRequest()->getParam('type', null);
        $themeId = $this->getRequest()->getParam('theme_id', null);

        if ($instanceId) {
            $widgetInstance->load($instanceId);
            if (!$widgetInstance->getId()) {
                $this->_getSession()->addError(
                    __('Please specify a correct widget.')
                );
                return false;
            }
        } else {
            $widgetInstance->setType($type)->setThemeId($themeId);
        }
        $this->_coreRegistry->register('current_widget_instance', $widgetInstance);
        return $widgetInstance;
    }

    /**
     * Widget Instances Grid
     *
     */
    public function indexAction()
    {
        $this->_title(__('Frontend Apps'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New widget instance action (forward to edit action)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit widget instance action
     *
     */
    public function editAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance) {
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($widgetInstance->getId() ? $widgetInstance->getTitle() : __('New Frontend App Instance'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Set body to response
     *
     * @param string $body
     * @return null
     */
    private function setBody($body)
    {
        $this->_translator->processResponseBody($body);

        $this->getResponse()->setBody($body);
    }

    /**
     * Validate action
     *
     */
    public function validateAction()
    {
        $response = new Magento_Object();
        $response->setError(false);
        $widgetInstance = $this->_initWidgetInstance();
        $result = $widgetInstance->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->setBody($response->toJson());
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance) {
            $this->_redirect('*/*/');
            return;
        }
        $widgetInstance->setTitle($this->getRequest()->getPost('title'))
            ->setStoreIds($this->getRequest()->getPost('store_ids', array(0)))
            ->setSortOrder($this->getRequest()->getPost('sort_order', 0))
            ->setPageGroups($this->getRequest()->getPost('widget_instance'))
            ->setWidgetParameters($this->getRequest()->getPost('parameters'));
        try {
            $widgetInstance->save();
            $this->_getSession()->addSuccess(
                __('The widget instance has been saved.')
            );
            if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'instance_id' => $widgetInstance->getId(),
                        '_current' => true
                    ));
            } else {
                $this->_redirect('*/*/');
            }
            return;
        } catch (Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
            $this->_logger->logException($exception);
            $this->_redirect('*/*/edit', array('_current' => true));
            return;
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Delete Action
     *
     */
    public function deleteAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if ($widgetInstance) {
            try {
                $widgetInstance->delete();
                $this->_getSession()->addSuccess(
                    __('The widget instance has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Categories chooser Action (Ajax request)
     *
     */
    public function categoriesAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $isAnchorOnly = $this->getRequest()->getParam('is_anchor_only', 0);
        $chooser = $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Catalog_Category_Widget_Chooser')
            ->setUseMassaction(true)
            ->setId($this->_objectManager->get('Magento_Core_Helper_Data')->uniqHash('categories'))
            ->setIsAnchorOnly($isAnchorOnly)
            ->setSelectedCategories(explode(',', $selected));
        $this->setBody($chooser->toHtml());
    }

    /**
     * Products chooser Action (Ajax request)
     *
     */
    public function productsAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $productTypeId = $this->getRequest()->getParam('product_type_id', '');
        $chooser = $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Widget_Chooser')
            ->setName($this->_objectManager->get('Magento_Core_Helper_Data')->uniqHash('products_grid_'))
            ->setUseMassaction(true)
            ->setProductTypeId($productTypeId)
            ->setSelectedProducts(explode(',', $selected));
        /* @var $serializer Magento_Adminhtml_Block_Widget_Grid_Serializer */
        $serializer = $this->getLayout()->createBlock(
            'Magento_Adminhtml_Block_Widget_Grid_Serializer',
            '',
            array(
                'data' => array(
                    'grid_block' => $chooser,
                    'callback' => 'getSelectedProducts',
                    'input_element_name' => 'selected_products',
                    'reload_param_name' => 'selected_products'
                )
            )
        );
        $this->setBody($chooser->toHtml() . $serializer->toHtml());
    }

    /**
     * Blocks Action (Ajax request)
     *
     */
    public function blocksAction()
    {
        /* @var $widgetInstance Magento_Widget_Model_Widget_Instance */
        $widgetInstance = $this->_initWidgetInstance();
        $layout = $this->getRequest()->getParam('layout');
        $selected = $this->getRequest()->getParam('selected', null);
        $blocksChooser = $this->getLayout()
            ->createBlock('Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container')
            ->setValue($selected)
            ->setArea($widgetInstance->getArea())
            ->setTheme($widgetInstance->getThemeId())
            ->setLayoutHandle($layout)
            ->setAllowedContainers($widgetInstance->getWidgetSupportedContainers());
        $this->setBody($blocksChooser->toHtml());
    }

    /**
     * Templates Chooser Action (Ajax request)
     *
     */
    public function templateAction()
    {
        /* @var $widgetInstance Magento_Widget_Model_Widget_Instance */
        $widgetInstance = $this->_initWidgetInstance();
        $block = $this->getRequest()->getParam('block');
        $selected = $this->getRequest()->getParam('selected', null);
        $templateChooser = $this->getLayout()
            ->createBlock('Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Template')
            ->setSelected($selected)
            ->setWidgetTemplates($widgetInstance->getWidgetSupportedTemplatesByContainer($block));
        $this->setBody($templateChooser->toHtml());
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Widget::widget_instance');
    }
}
