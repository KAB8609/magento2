<?php
/**
 * Category controller
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action {
    /**
     * View category products
     *
     */
    function viewAction()
    {
        $action = 'catalog_category_'.$this->getRequest()->getParam('id', false);
        $this->loadLayout();
            
        $category = Mage::getModel('catalog', 'category')
            ->load($this->getRequest()->getParam('id', false));
            
        // Valid category id
        if (!$category->isEmpty()) {
            $block = $this->getLayout()->createBlock('catalog_category_view', 'category.products', array('category'=>$category));
            $block->loadData($this->getRequest());
            
            $this->getLayout()->getBlock('content')->append($block);
        }
        else {
            $this->_forward('noRoute');
            return ;
        }
        
        $this->renderLayout();
    }
    
    public function filterAction()
    {
        
    }

    function fillAction()
    {
        set_time_limit(0);

        /**
         * @var $db Zend_Db_Adapter_Abstract
         */
        $db = Mage::getConfig()->getResource('catalog_write')->getConnection();

        for ($i=0;$i<100;$i++) {
            $base = array();
            $base['create_date'] = date('Y-m-d H:i:s');
            $base['attribute_set_id'] = 1;

            $db->insert('catalog_product', $base);
            $product_id = $db->lastInsertId();
            $category_id   = rand(3,23);

            $cat_data = array();
            $cat_data['product_id'] = $product_id;
            $cat_data['category_id']= rand(3,23);
            $cat_data['position']   = 1;

            $db->insert('catalog_category_product', $cat_data);
            $new_cat_id = $cat_data['category_id']+1;

            if ($new_cat_id>23) {
                $new_cat_id=$new_cat_id-2;
            }

            $cat_data['category_id'] = $new_cat_id;
            $db->insert('catalog_category_product', $cat_data);

            for ($website=1;$website<=2;$website++) {
                /**
                 * 1 - name
                 * 2 - description
                 * 3 - image
                 * 4 - model
                 * 5 - price
                 * 6 - cost
                 * 7 - add_date
                 * 8 - weight
                 * 9 - status
                 * 10- manufacturers
                 */
                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 1;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'Product #' . $product_id;
                $db->insert('catalog_product_attribute_varchar', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 2;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'Product #' . $product_id . ' description';
                $db->insert('catalog_product_attribute_text', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 3;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'product_small_image.jpg';
                $db->insert('catalog_product_attribute_varchar', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 4;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 'MDL'.$product_id;
                $db->insert('catalog_product_attribute_varchar', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 5;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,100);
                $attr['attribute_qty']  = 1;
                $db->insert('catalog_product_attribute_decimal', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 6;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,100);
                $attr['attribute_qty']  = 1;
                $db->insert('catalog_product_attribute_decimal', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 7;
                $attr['website_id']     = $website;
                $attr['attribute_value']= new Zend_Db_Expr('NOW()');;
                $db->insert('catalog_product_attribute_date', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 8;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,100);
                $attr['attribute_qty']  = 1;
                $db->insert('catalog_product_attribute_decimal', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 9;
                $attr['website_id']     = $website;
                $attr['attribute_value']= 1;
                $db->insert('catalog_product_attribute_int', $attr);

                $attr = array();
                $attr['product_id']     = $product_id;
                $attr['attribute_id']   = 10    ;
                $attr['website_id']     = $website;
                $attr['attribute_value']= rand(1,10);
                $db->insert('catalog_product_attribute_int', $attr);

            }
        }
    }
}
