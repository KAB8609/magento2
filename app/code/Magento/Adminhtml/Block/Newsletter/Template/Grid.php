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
 * Adminhtml newsletter templates grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Newsletter_Template_Grid extends Magento_Backend_Block_Widget_Grid_Extended
{
    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $urlModel, $data);
        $this->setEmptyText(Mage::helper('Magento_Newsletter_Helper_Data')->__('No Templates Found'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('Magento_Newsletter_Model_Resource_Template_Collection')
            ->useOnlyActual();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('template_code',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('ID'),
                'index'     => 'template_id',
                'header_css_class'  => 'col-id',
                'column_css_class'  => 'col-id'
        ));
        $this->addColumn('code',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Template'),
                'index'     => 'template_code',
                'header_css_class'  => 'col-template',
                'column_css_class'  => 'col-template'
        ));

        $this->addColumn('added_at',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Added'),
                'index'     => 'added_at',
                'gmtoffset' => true,
                'type'      => 'datetime',
                'header_css_class'  => 'col-added',
                'column_css_class'  => 'col-added'
        ));

        $this->addColumn('modified_at',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Updated'),
                'index'     => 'modified_at',
                'gmtoffset' => true,
                'type'      => 'datetime',
                'header_css_class'  => 'col-updated',
                'column_css_class'  => 'col-updated'
        ));

        $this->addColumn('subject',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Subject'),
                'index'     => 'template_subject',
                'header_css_class'  => 'col-subject',
                'column_css_class'  => 'col-subject'
        ));

        $this->addColumn('sender',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Sender'),
                'index'     => 'template_sender_email',
                'renderer'  => 'Magento_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Sender',
                'header_css_class'  => 'col-sender',
                'column_css_class'  => 'col-sender'
        ));

        $this->addColumn('type',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Template Type'),
                'index'     => 'template_type',
                'type'      => 'options',
                'options'   => array(
                    Magento_Newsletter_Model_Template::TYPE_HTML   => 'html',
                    Magento_Newsletter_Model_Template::TYPE_TEXT 	=> 'text'
                ),
                'header_css_class'  => 'col-type',
                'column_css_class'  => 'col-type'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Magento_Newsletter_Helper_Data')->__('Action'),
                'index'     => 'template_id',
                'sortable'  => false,
                'filter'    => false,
                'no_link'   => true,
                'renderer'  => 'Magento_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action',
                'header_css_class'  => 'col-actions',
                'column_css_class'  => 'col-actions'
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

