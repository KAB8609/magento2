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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_UrlrewriteController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Create initial action
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminhtml/urlrewrite');
        return $this;
    }

    protected function _initUrlrewrite($idFieldName = 'id')
    {
        $id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('urlrewrite/urlrewrite');

        if ($id) {
            $model->load($id);
        }

        Mage::register('urlrewrite_urlrewrite', $model);
        return $this;
    }

    /**
     * Create index url action
     */
    public function indexAction()
    {
    	$this->_initAction();

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/urlrewrite')
        );

        $this->renderLayout();
    }


    /**
     * Delete urlrewrite action
     */
    public function deleteAction()
    {
        $this->_initUrlrewrite();
        $model = Mage::registry('urlrewrite_urlrewrite');
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Urlrewrite was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/urlrewrite');
    }

    /**
     * Create edit url action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('urlrewrite/urlrewrite');
        if ($model) {
        	$model->load($id);
        }


//        if (!$model->getId()) {
//            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This url no longer exists'));
//            $this->_redirect('*/*/');
//           return;
//        }

        Mage::register('urlrewrite_urlrewrite', $model);

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Url'),  Mage::helper('adminhtml')->__('Edit Url'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_edit'))
            ->renderLayout();
    }

    /**
     * Create new url action
     */
    public function newAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminhtml/urlrewrite');

        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_add'));

        $this->renderLayout();
//        $this->_forward('edit');
    }

    /**
     * Save urlrewrite action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $this->_initUrlrewrite();
            $model = Mage::registry('urlrewrite_urlrewrite');

            // Saving urlrewrite data
            try {
            	if (!$model->getId()) {
            		$model->setType($data['type']);
            		$model->setStoreId($data['store_id']);
					$model->setIdPath($data['id_path']);
            	}
            	$model->setRequestPath($data['request_path']);
            	$model->setTargetPath($data['target_path']);
            	$model->setOptions($data['options']);
				$model->setDescription($data['description']);
            	$model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Urlrewrite was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect(Mage::getUrl('*/urlrewrite/edit', array('id'=>$model->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/urlrewrite'));
    }

    public function jsonProductInfoAction()
    {
        $response = new Varien_Object();
        $id = $this->getRequest()->getParam('id');
        if( intval($id) > 0 ) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $response->setId($id);
            $response->addData($product->getData());
            $response->setError(0);
        } else {
            $response->setError(1);
            $response->setMessage(Mage::helper('adminhtml')->__('Unable to get product id.'));
        }
        $this->getResponse()->setBody($response->toJSON());
    }

    public function getCategoryInfoAction()
    {
        $response = new Varien_Object();
        $id = $this->getRequest()->getParam('id');
        if( intval($id) > 0 ) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            //$response->setId($id);
            //$response->addData($product->getData());
            //$response->setError(0);
            Mage::register('product', $product);
            $tree = new Mage_Adminhtml_Block_Urlrewrite_Category_Tree();
            //$response->addData($tree->getTreeJson());
        } else {
            $response->setError(1);
            $response->setMessage(Mage::helper('adminhtml')->__('Unable to get product id.'));
        }
       // $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/urlrewrite_category_tree')->toHtml());
       $this->getResponse()->setBody($tree->getTreeJson());
    }
}