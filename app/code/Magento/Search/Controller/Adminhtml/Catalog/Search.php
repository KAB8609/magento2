<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search controller(ajax grid)
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Controller\Adminhtml\Catalog;

class Search extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Ajax grid action
     */
    public function relatedGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = \Mage::getModel('\Magento\CatalogSearch\Model\Query');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This search no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        \Mage::register('current_catalog_search', $model);

        $this->loadLayout(false);
        $this->renderLayout();
    }
}
