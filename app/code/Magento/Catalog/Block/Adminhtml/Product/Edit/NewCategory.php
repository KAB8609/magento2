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
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class NewCategory extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $registry, $formFactory, $data);
        $this->setUseContainer(true);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Form preparation
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'new_category_form',
            ))
        );
        $form->setUseContainer($this->getUseContainer());

        $form->addField('new_category_messages', 'note', array());

        $fieldset = $form->addFieldset('new_category_form_fieldset', array());

        $fieldset->addField('new_category_name', 'text', array(
            'label'    => __('Category Name'),
            'title'    => __('Category Name'),
            'required' => true,
            'name'     => 'new_category_name',
        ));

        $fieldset->addField('new_category_parent', 'select', array(
            'label'    => __('Parent Category'),
            'title'    => __('Parent Category'),
            'required' => true,
            'options'  => $this->_getParentCategoryOptions(),
            'class'    => 'validate-parent-category',
            'name'     => 'new_category_parent',
            // @codingStandardsIgnoreStart
            'note'     => __('If there are no custom parent categories, please use the default parent category. You can reassign the category at any time in <a href="%1" target="_blank">Products > Categories</a>.', $this->getUrl('catalog/category')),
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
        return $this->getUrl('catalog/category/save');
    }

    /**
     * Attach new category dialog widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $widgetOptions = $this->_coreData->jsonEncode(array(
            'suggestOptions' => array(
                'source' => $this->getUrl('catalog/category/suggestCategories'),
                'valueField' => '#new_category_parent',
                'className' => 'category-select',
                'multiselect' => true,
                'showAll' => true,
            ),
            'saveCategoryUrl' => $this->getUrl('catalog/category/save'),
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