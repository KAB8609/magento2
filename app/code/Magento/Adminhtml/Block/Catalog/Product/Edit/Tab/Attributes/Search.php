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
 * New attribute panel on product edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes_Search extends Magento_Backend_Block_Widget
{
    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Magento_Catalog::product/edit/attribute/search.phtml');
        parent::_construct();
    }

    /**
     * @return array
     */
    public function getSelectorOptions()
    {
        $templateId = Mage::registry('product')->getAttributeSetId();
        return array(
            'source' => $this->getUrl('*/catalog_product/suggestAttributes'),
            'minLength' => 0,
            'ajaxOptions' => array('data' => array('template_id' => $templateId)),
            'template' => '[data-template-for="product-attribute-search"]',
            'data' => $this->getSuggestedAttributes('', $templateId),
        );
    }

    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @param int $templateId
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getSuggestedAttributes($labelPart, $templateId = null)
    {
        $escapedLabelPart = Mage::getResourceHelper('Magento_Core')
            ->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Attribute_Collection')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart));

        $collection->setExcludeSetFilter($templateId ?: $this->getRequest()->getParam('template_id'))->setPageSize(20);

        $result = array();
        foreach ($collection->getItems() as $attribute) {
            /** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            $result[] = array(
                'id'      => $attribute->getId(),
                'label'   => $attribute->getFrontendLabel(),
                'code'    => $attribute->getAttributeCode(),
            );
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getAddAttributeUrl()
    {
        return $this->getUrl('*/catalog_product/addAttributeToTemplate');
    }
}