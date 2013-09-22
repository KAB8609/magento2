<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content types mapping form block
 */

namespace Magento\GoogleShopping\Block\Adminhtml\Types\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\GoogleShopping\Helper\Category|null
     */
    protected $_googleShoppingCategory = null;

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magento\Data\Form\Factory
     */
    protected $_formFactory;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Data\Form\Element\Factory $elementFactory
     * @param \Magento\GoogleShopping\Helper\Category $googleShoppingCategory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Data\Form\Element\Factory $elementFactory,
        \Magento\GoogleShopping\Helper\Category $googleShoppingCategory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_googleShoppingCategory = $googleShoppingCategory;
        $this->_elementFactory = $elementFactory;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Types\Edit\Form
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $itemType = $this->getItemType();

        $fieldset = $form->addFieldset('content_fieldset', array(
            'legend'    => __('Attribute set mapping')
        ));

        if ( !($targetCountry = $itemType->getTargetCountry()) ) {
            $isoKeys = array_keys($this->_getCountriesArray());
            $targetCountry = isset($isoKeys[0]) ? $isoKeys[0] : null;
        }
        $countrySelect = $fieldset->addField('select_target_country', 'select', array(
            'label'     => __('Target Country'),
            'title'     => __('Target Country'),
            'name'      => 'target_country',
            'required'  => true,
            'options'   => $this->_getCountriesArray(),
            'value'     => $targetCountry,
        ));
        if ($itemType->getTargetCountry()) {
            $countrySelect->setDisabled(true);
        }

        $attributeSetsSelect = $this->getAttributeSetsSelectElement($targetCountry)
            ->setValue($itemType->getAttributeSetId());
        if ($itemType->getAttributeSetId()) {
            $attributeSetsSelect->setDisabled(true);
        }

        $fieldset->addField('attribute_set', 'note', array(
            'label'     => __('Attribute Set'),
            'title'     => __('Attribute Set'),
            'required'  => true,
            'text'      => '<div id="attribute_set_select">' . $attributeSetsSelect->toHtml() . '</div>',
        ));

        $categories = $this->_googleShoppingCategory->getCategories();
        $fieldset->addField('category', 'select', array(
            'label'     => __('Google Product Category'),
            'title'     => __('Google Product Category'),
            'required'  => true,
            'name'      => 'category',
            'options'   => array_combine($categories, array_map('htmlspecialchars_decode', $categories)),
            'value'      => $itemType->getCategory(),
        ));

        $attributesBlock = $this->getLayout()
            ->createBlock('Magento\GoogleShopping\Block\Adminhtml\Types\Edit\Attributes')
            ->setTargetCountry($targetCountry);
        if ($itemType->getId()) {
            $attributesBlock->setAttributeSetId($itemType->getAttributeSetId())
                ->setAttributeSetSelected(true);
        }

        $attributes = $this->_coreRegistry->registry('attributes');
        if (is_array($attributes) && count($attributes) > 0) {
            $attributesBlock->setAttributesData($attributes);
        }

        $fieldset->addField('attributes_box', 'note', array(
            'label'     => __('Attributes Mapping'),
            'text'      => '<div id="attributes_details">' . $attributesBlock->toHtml() . '</div>',
        ));

        $form->addValues($itemType->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getSaveUrl());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get Select field with list of available attribute sets for some target country
     *
     * @param  string $targetCountry
     * @return \Magento\Data\Form\Element\Select
     */
    public function getAttributeSetsSelectElement($targetCountry)
    {
        $field = $this->_elementFactory->create('select');
        $field->setName('attribute_set_id')
            ->setId('select_attribute_set')
            ->setForm($this->_formFactory->create())
            ->addClass('required-entry')
            ->setValues($this->_getAttributeSetsArray($targetCountry));
        return $field;
    }

    /**
     * Get allowed country names array
     *
     * @return array
     */
    protected function _getCountriesArray()
    {
        $_allowed = \Mage::getSingleton('Magento\GoogleShopping\Model\Config')->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[$iso] = $info['name'];
        }
        return $result;
    }

    /**
     * Get array with attribute setes which available for some target country
     *
     * @param  string $targetCountry
     * @return array
     */
    protected function _getAttributeSetsArray($targetCountry)
    {
        $entityType = \Mage::getModel('Magento\Catalog\Model\Product')->getResource()->getEntityType();
        $collection = \Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter($entityType->getId());

        $ids = array();
        $itemType = $this->getItemType();
        if (!($itemType instanceof \Magento\Object && $itemType->getId())) {
            $typesCollection = \Mage::getResourceModel('Magento\GoogleShopping\Model\Resource\Type\Collection')
                ->addCountryFilter($targetCountry)
                ->load();
            foreach ($typesCollection as $type) {
                $ids[] = $type->getAttributeSetId();
            }
        }

        $result = array('' => '');
        foreach ($collection as $attributeSet) {
            if (!in_array($attributeSet->getId(), $ids)) {
                $result[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
            }
        }
        return $result;
    }

    /**
     * Get current attribute set mapping from register
     *
     * @return \Magento\GoogleShopping\Model\Type
     */
    public function getItemType()
    {
        return $this->_coreRegistry->registry('current_item_type');
    }

    /**
     * Get URL for saving the current map
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('type_id' => $this->getItemType()->getId()));
    }
}
