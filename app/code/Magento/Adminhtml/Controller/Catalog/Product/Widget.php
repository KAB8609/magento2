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
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Catalog\Product;

class Widget extends \Magento\Adminhtml\Controller\Action
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
            '\Magento\Adminhtml\Block\Catalog\Product\Widget\Chooser',
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
                '\Magento\Adminhtml\Block\Catalog\Category\Widget\Chooser',
                '',
                array(
                    'data' => array(
                        'id'                  => $uniqId . 'Tree',
                        'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                        'with_empty_node'     => true
                    )
                )
            );

            $html = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Widget\Chooser\Container')
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }

        $this->getResponse()->setBody($html);
    }
}
