<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Catalog_Product_Widget extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $productTypeId = $this->getRequest()->getParam('product_type_id', null);

        $productsGrid = $this->getLayout()->createBlock(
            'Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser',
            '',
            array(
                'data' => array(
                    'id'              => $uniqId,
                    'use_massaction'  => $massAction,
                    'product_type_id' => $productTypeId,
                    'category_id'     => $this->getRequest()->getParam('category_id')
                )
            )
        );

        $html = $productsGrid->toHtml();

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $this->getLayout()->createBlock(
                'Mage_Adminhtml_Block_Catalog_Category_Widget_Chooser',
                '',
                array(
                    'data' => array(
                        'id'                  => $uniqId . 'Tree',
                        'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                        'with_empty_node'     => true
                    )
                )
            );

            $html = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser_Container')
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }

        $this->getResponse()->setBody($html);
    }
}
