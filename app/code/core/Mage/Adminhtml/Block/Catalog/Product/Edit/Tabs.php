<?php
/**
 * admin product edit tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(__('Product Information'));
    }

    protected function _initChildren()
    {
        if (!($setId = Mage::registry('product')->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        
        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->load();
                
            foreach ($groupCollection as $group) {
                $this->addTab($group->getAttributeGroupName().'_group', array(
                    'label'     => __($group->getAttributeGroupName()),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_attributes')
                        ->setGroup($group)
                        ->toHtml(),
                ));
            }
            
            $this->addTab('stores', array(
                'label'     => __('Stores'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_stores')->toHtml(),
            ));
            
            $this->addTab('categories', array(
                'label'     => __('Categories'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_categories')->toHtml(),
            ));
    
            /*$this->addTab('related', array(
                'label'     => __('Related Products'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related', 'admin.related.products')->toHtml(),
            ));*/
        }
        else {
            $this->addTab('set', array(
                'label'     => __('Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml(),
                'active'    => true
            ));
        }
    }
}
