<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Controller_Promo_Widget extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->getLayout()->createBlock(
                    'Magento_Adminhtml_Block_Promo_Widget_Chooser_Sku', 'promo_widget_chooser_sku',
                    array('data' => array('js_form_object' => $request->getParam('form')),
                ));
                break;

            case 'category_ids':
                $ids = $request->getParam('selected', array());
                if (is_array($ids)) {
                    foreach ($ids as $key => &$id) {
                        $id = (int) $id;
                        if ($id <= 0) {
                            unset($ids[$key]);
                        }
                    }

                    $ids = array_unique($ids);
                } else {
                    $ids = array();
                }


                $block = $this->getLayout()->createBlock(
                        'Magento_Adminhtml_Block_Catalog_Category_Checkboxes_Tree', 'promo_widget_chooser_category_ids',
                        array('data' => array('js_form_object' => $request->getParam('form')))
                    )
                    ->setCategoryIds($ids)
                ;
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogRule::promo_catalog');
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        $categoryId = (int) $this->getRequest()->getPost('id');
        if ($categoryId) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Category_Tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Initialize category object in registry
     *
     * @return Magento_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id',false);
        $storeId    = (int)$this->getRequest()->getParam('store');

        $category   = $this->_objectManager->create('Magento_Catalog_Model_Category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = $this->_objectManager->get('Magento_Core_Model_StoreManager')
                    ->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current' => true, 'id' => null));
                    return false;
                }
            }
        }

        $this->_coreRegistry->register('category', $category);
        $this->_coreRegistry->register('current_category', $category);

        return $category;
    }
}
