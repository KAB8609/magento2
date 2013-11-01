<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

/**
 * Product form category field helper
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Category extends \Magento\Data\Form\Element\Multiselect
{
    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $collectionFactory
     * @param \Magento\Escaper $escaper
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $attributes
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $collectionFactory,
        \Magento\Escaper $escaper,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\View\LayoutInterface $layout,
        \Magento\Core\Helper\Data $coreData,
        array $attributes = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_backendData = $backendData;
        $this->_coreData = $coreData;
        parent::__construct($escaper, $factoryElement, $factoryCollection, $attributes);
        $this->_layout = $layout;
    }

    /**
     * Get values for select
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = array();

        foreach ($collection as $category) {
            $options[] = array(
                'label' => $category->getName(),
                'value' => $category->getId()
            );
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function _getCategoriesCollection()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->_coreData->jsonEncode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');

        $button = $this->_layout
            ->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData(array(
                'id'        => 'add_category_button',
                'label'     => $newCategoryCaption,
                'title'     => $newCategoryCaption,
                'onclick'   => 'jQuery("#new-category").dialog("open")'
            ));
        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        jQuery('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
    </script>
HTML;
        return $return . $button->toHtml();
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => $this->_backendData
                ->getUrl('catalog/category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
