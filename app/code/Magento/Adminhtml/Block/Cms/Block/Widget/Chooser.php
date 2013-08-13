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
 * CMS block chooser for Wysiwyg CMS widget
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Block_Widget_Chooser extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('block_identifier');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_is_active' => '1'));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element Form Element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('Magento_Core_Helper_Data')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/cms_block_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('Mage_Widget_Block_Adminhtml_Widget_Chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $block = Mage::getModel('Magento_Cms_Model_Block')->load($element->getValue());
            if ($block->getId()) {
                $chooser->setLabel($block->getTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(blockId);
                '.$chooserJsObject.'.setElementLabel(blockTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare Cms static blocks collection
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Magento_Cms_Model_Block')->getCollection();
        /* @var $collection Magento_Cms_Model_Resource_Block_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for Cms blocks grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', array(
            'header'    => Mage::helper('Magento_Cms_Helper_Data')->__('ID'),
            'align'     => 'right',
            'index'     => 'block_id',
            'width'     => 50
        ));

        $this->addColumn('chooser_title', array(
            'header'    => Mage::helper('Magento_Cms_Helper_Data')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('chooser_identifier', array(
            'header'    => Mage::helper('Magento_Cms_Helper_Data')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));


        $this->addColumn('chooser_is_active', array(
            'header'    => Mage::helper('Magento_Cms_Helper_Data')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('Magento_Cms_Helper_Data')->__('Disabled'),
                1 => Mage::helper('Magento_Cms_Helper_Data')->__('Enabled')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/cms_block_widget/chooser', array('_current' => true));
    }
}
