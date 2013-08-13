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
 * Admin ratings controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Controller_Rating extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $this->_setActiveMenu('Mage_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Manage Ratings'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Manage Ratings'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $ratingModel = Mage::getModel('Magento_Rating_Model_Rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }

        $this->_title($ratingModel->getId() ? $ratingModel->getRatingCode() : $this->__('New Rating'));

        $this->_setActiveMenu('Mage_Review::catalog_reviews_ratings_ratings');
        $this->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Manage Ratings'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Manage Ratings'));

        $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit'))
            ->_addLeft($this->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit_Tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save rating
     */
    public function saveAction()
    {
        $this->_initEnityId();

        if ($this->getRequest()->getPost()) {
            try {
                $ratingModel = Mage::getModel('Magento_Rating_Model_Rating');

                $stores = $this->getRequest()->getParam('stores');
                $position = (int)$this->getRequest()->getParam('position');
                $stores[] = 0;
                $isActive = (bool)$this->getRequest()->getParam('is_active');
                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                    ->setRatingCodes($this->getRequest()->getParam('rating_codes'))
                    ->setStores($stores)
                    ->setPosition($position)
                    ->setId($this->getRequest()->getParam('id'))
                    ->setIsActive($isActive)
                    ->setEntityId(Mage::registry('entityId'))
                    ->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = Mage::getModel('Magento_Rating_Model_Rating_Option');
                        if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setRatingId($ratingModel->getId())
                            ->setPosition($i)
                            ->save();
                        $i++;
                    }
                }

                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(Mage::helper('Magento_Adminhtml_Helper_Data')->__('You saved the rating.'));
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setRatingData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setRatingData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('Magento_Rating_Model_Rating');
                /* @var $model Magento_Rating_Model_Rating */
                $model->load($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(Mage::helper('Magento_Adminhtml_Helper_Data')->__('You deleted the rating.'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _initEnityId()
    {
        $this->_title($this->__('Ratings'));

        Mage::register('entityId', Mage::getModel('Magento_Rating_Model_Rating_Entity')->getIdByCode('product'));
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rating::ratings');
    }

}
