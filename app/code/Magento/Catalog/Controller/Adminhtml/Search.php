<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Search extends \Magento\Backend\App\Action
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
        Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\App\Action\Title $title
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->_title = $title;
    }

    protected function _initAction()
    {
        $this->_layoutServices->loadLayout();
        $this->_setActiveMenu('Magento_CatalogSearch::catalog_search')
            ->_addBreadcrumb(__('Search'), __('Search'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title->add(__('Search Terms'));

        $this->_initAction()
            ->_addBreadcrumb(__('Catalog'), __('Catalog'));
            $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title->add(__('Search Terms'));

        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('This search no longer exists.'));
                $this->_redirect('catalog/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Adminhtml\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_catalog_search', $model);

        $this->_initAction();

        $this->_title->add($id ? $model->getQueryText() : __('New Search'));

        $this->_layoutServices->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

        $this->_layoutServices->getLayout()->getBlock('adminhtml.catalog.search.edit')
            ->setData('action', $this->getUrl('catalog/search/save'));

        $this
            ->_addBreadcrumb($id ? __('Edit Search') : __('New Search'), $id ? __('Edit Search') : __('New Search'));

        $this->renderLayout();
    }

    /**
     * Save search query
     *
     */
    public function saveAction()
    {
        $hasError   = false;
        $data       = $this->getRequest()->getPost();
        $queryId    = $this->getRequest()->getPost('query_id', null);
        if ($this->getRequest()->isPost() && $data) {
            /* @var $model \Magento\CatalogSearch\Model\Query */
            $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query');

            // validate query
            $queryText  = $this->getRequest()->getPost('query_text', false);
            $storeId    = $this->getRequest()->getPost('store_id', false);

            try {
                if ($queryText) {
                    $model->setStoreId($storeId);
                    $model->loadByQueryText($queryText);
                    if ($model->getId() && $model->getId() != $queryId) {
                        throw new \Magento\Core\Exception(
                            __('You already have an identical search term query.')
                        );
                    } else if (!$model->getId() && $queryId) {
                        $model->load($queryId);
                    }
                } else if ($queryId) {
                    $model->load($queryId);
                }

                $model->addData($data);
                $model->setIsProcessed(0);
                $model->save();

            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $hasError = true;
            } catch (\Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong while saving the search query.')
                );
                $hasError = true;
            }
        }

        if ($hasError) {
            $this->_getSession()->setPageData($data);
            $this->_redirect('catalog/*/edit', array('id' => $queryId));
        } else {
            $this->_redirect('catalog/*');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query');
                $model->setId($id);
                $model->delete();
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(__('You deleted the search.'));
                $this->_redirect('catalog/*/');
                return;
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('catalog/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('We can\'t find a search term to delete.'));
        $this->_redirect('catalog/*/');
    }

    public function massDeleteAction()
    {
        $searchIds = $this->getRequest()->getParam('search');
        if (!is_array($searchIds)) {
             $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Please select catalog searches.'));
        } else {
            try {
                foreach ($searchIds as $searchId) {
                    $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query')->load($searchId);
                    $model->delete();
                }
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('Total of %1 record(s) were deleted', count($searchIds))
                );
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('catalog/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogSearch::search');
    }
}
