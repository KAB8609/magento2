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
 * New category creation form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_NewCategory extends Mage_Backend_Block_Widget_Form
{
    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Form preparation
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'new_category_form'));
        $form->setUseContainer($this->getUseContainer());

        $form->addField('new_category_messages', 'note', array());

        $fieldset = $form->addFieldset('new_category_form_fieldset', array());

        $fieldset->addField('new_category_name', 'text', array(
            'label'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Category Name'),
            'title'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Category Name'),
            'required' => true,
            'name'     => 'new_category_name',
        ));

        $fieldset->addField('new_category_parent', 'select', array(
            'label'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Parent Category'),
            'title'    => Mage::helper('Magento_Catalog_Helper_Data')->__('Parent Category'),
            'required' => true,
            'options'  => $this->_getParentCategoryOptions(),
            'class'    => 'validate-parent-category',
            'name'     => 'new_category_parent',
            // @codingStandardsIgnoreStart
            'note'     => Mage::helper('Magento_Catalog_Helper_Data')->__('If there are no custom parent categories, please use the default parent category. You can reassign the category at any time in <a href="%s" target="_blank">Products > Categories</a>.', $this->getUrl('*/catalog_category')),
            // @codingStandardsIgnoreEnd
        ));

        $this->setForm($form);
    }

    /**
     * Get parent category options
     *
     * @return array
     */
    protected function _getParentCategoryOptions()
    {
        $items = $this->_categoryFactory->create()
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSort('entity_id', 'ASC')
            ->setPageSize(3)
            ->load()
            ->getItems();

        return count($items) === 2
            ? array($items[2]->getEntityId() => $items[2]->getName())
            : array();
    }

    /**
     * Category save action URL
     *
     * @return string
     */
    public function getSaveCategoryUrl()
    {
        return $this->getUrl('adminhtml/catalog_category/save');
    }

    /**
     * Attach new category dialog widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        /** @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper = Mage::helper('Magento_Core_Helper_Data');
        $widgetOptions = $coreHelper->jsonEncode(array(
            'suggestOptions' => array(
                'source' => $this->getUrl('adminhtml/catalog_category/suggestCategories'),
                'valueField' => '#new_category_parent',
                'className' => 'category-select',
                'multiselect' => true,
                'showAll' => true,
            ),
            'saveCategoryUrl' => $this->getUrl('adminhtml/catalog_category/save'),
        ));
        return <<<HTML
<script>
    jQuery(function($) { // waiting for page to load to have '#category_ids-template' available
        $('#new-category').mage('newCategoryDialog', $widgetOptions);
    });
</script>
HTML;
    }
}
