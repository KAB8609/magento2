<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable
    extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Reference to product objects that is being edited
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_config = null;

    protected $_template = 'product/edit/downloadable.phtml';

    /**
     * Get tab URL
     *
     * @return string
     */
//    public function getTabUrl()
//    {
//        return $this->getUrl('downloadable/product_edit/form', array('_current' => true));
//    }

    /**
     * Get tab class
     *
     * @return string
     */
//    public function getTabClass()
//    {
//        return 'ajax';
//    }

    /**
     * Check is readonly block
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getDownloadableReadonly();
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Downloadable Information');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Downloadable Information');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs::ADVANCED_TAB_GROUP_CODE;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $accordion = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Accordion')
            ->setId('downloadableInfo');

        $accordion->addItem('samples', array(
            'title'   => __('Samples'),
            'content' => $this->getLayout()
                ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples')
                ->toHtml(),
            'open'    => false,
        ));

        $accordion->addItem('links', array(
            'title'   => __('Links'),
            'content' => $this->getLayout()->createBlock(
                'Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links',
                'catalog.product.edit.tab.downloadable.links')->toHtml(),
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }

}
