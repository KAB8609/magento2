<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product tags admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/tag')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tags'), Mage::helper('adminhtml')->__('Tags'))
        ;
        return $this;
    }

    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Tag_Edit_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('adminhtml/tag_edit_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName)
        ;
    }

    public function indexAction()
    {
        /**
         * setting status parameter for grid filter for non-ajax request
         *
         */
        if ($this->getRequest()->getParam('pending') && !$this->getRequest()->getParam('isAjax')) {
            $this->getRequest()->setParam('filter', base64_encode('status=' . Mage_Tag_Model_Tag::STATUS_PENDING));
        }
        elseif (!$this->getRequest()->getParam('isAjax')) {
            $this->getRequest()->setParam('filter', '');
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('All Tags'), Mage::helper('adminhtml')->__('All Tags'))
            ->_setActiveMenu('catalog/tag/all')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag'))
            ->renderLayout();
    }

    public function ajaxGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_tag_grid')->toHtml()
        );
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('*/*/*/', array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true));
            return;
        }

        $id = $this->getRequest()->getParam('tag_id');
        Mage::register('tagId', $id);
        $model = Mage::getModel('tag/tag');

        if ($id) {
            $model->load($id);
        }

        $model->addSummary($this->getRequest()->getParam('store'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('tag_tag', $model);

        $this->_initAction()->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            $data['base_popularity']    = (isset($postData['base_popularity'])) ? $postData['base_popularity'] : 0;
            $data['store_id']           = $postData['store_id'];

            $model = Mage::getModel('tag/tag');
            $model->setData($data);

            if (isset($postData['links']['related'])) {
                parse_str($postData['links']['related'], $productIds);
                $tagRelationModel = Mage::getModel('tag/tag_relation');
                $tagRelationModel->addRelations($model, array_keys($productIds));
            }

            switch( $this->getRequest()->getParam('ret') ) {
                case 'all':
                    $url = $this->getUrl('*/*/index', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                case 'pending':
                    $url = $this->getUrl('*/tag/pending', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                default:
                    $url = $this->getUrl('*/*/index', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
            }

            try {
                $model->save();
                $model->aggregate();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if ($this->getRequest()->getParam('ret') == 'edit') {
                    $url = $this->getUrl('*/tag/edit', array(
                        'tag_id' => $model->getId()
                    ));
                }

                $this->getResponse()->setRedirect($url);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        $this->getResponse()->setRedirect($url);
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('tag_id')) {

            switch( $this->getRequest()->getParam('ret') ) {
                case 'all':
                    $url = $this->getUrl('*/*/', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                case 'pending':
                    $url = $this->getUrl('*/tag/pending', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                default:
                    $url = $this->getUrl('*/*/', array(
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
            }

            try {
                $model = Mage::getModel('tag/tag');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tag was successfully deleted'));
                $this->getResponse()->setRedirect($url);
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find a tag to delete'));
        $this->getResponse()->setRedirect($url);
    }

    /**
     * Pending tags
     *
     */
    public function pendingAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Pending Tags'), Mage::helper('adminhtml')->__('Pending Tags'))
            ->_setActiveMenu('catalog/tag/pending')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_pending'))
            ->renderLayout();
    }

    /**
     * Assigned products (with serializer block)
     *
     */
    public function assignedAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));
        $store_id = $this->getRequest()->getParam('store');

        $relatedProducts = Mage::getModel('tag/tag')
            ->setTagId(Mage::registry('tagId'))
            ->setStoreId($store_id)
            ->getRelatedProducts();

        $assignedGridBlock = $this->getLayout()->createBlock('adminhtml/tag_assigned_grid');
        $serializerBlock = $this->_createSerializerBlock('links[related]', $assignedGridBlock, $relatedProducts);

        $this->getResponse()->setBody(
            $assignedGridBlock->toHtml() . $serializerBlock->toHtml()
        );
    }

    /**
     * Assigned products grid
     *
     */
    public function assignedGridOnlyAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_assigned_grid')->toHtml()
        );
    }

    /**
     * Tagged products
     *
     */
    public function productAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_product_grid')->toHtml()
        );
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/tag_customer_grid')->toHtml()
        );
    }

    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if(!is_array($tagIds)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s)'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')->load($tagId);
                    $tag->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
    }

    public function massStatusAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if(!is_array($tagIds)) {
            // No products selected
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select tag(s)'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('tag/tag')
                        ->load($tagId)
                        ->setStatus($this->getRequest()->getParam('status'));
                     $tag->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'pending':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/pending');
                break;
            case 'all':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/all');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag');
                break;
        }
    }
}
