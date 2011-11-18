<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter templates grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setEmptyText(Mage::helper('Mage_Newsletter_Helper_Data')->__('No Templates Found'));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('Mage_Newsletter_Model_Resource_Template_Collection')
            ->useOnlyActual();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('template_code',
            array('header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('ID'), 'align'=>'center', 'index'=>'template_id'));
        $this->addColumn('code',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Template Name'),
                   'index'=>'template_code'
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Subject'),
                'index'=>'template_subject'
        ));

        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Sender'
        ));

        $this->addColumn('type',
            array(
                'header'=>Mage::helper('Mage_Newsletter_Helper_Data')->__('Template Type'),
                'index'=>'template_type',
                'type' => 'options',
                'options' => array(
                    Mage_Newsletter_Model_Template::TYPE_HTML   => 'html',
                    Mage_Newsletter_Model_Template::TYPE_TEXT 	=> 'text'
                ),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('Mage_Newsletter_Helper_Data')->__('Action'),
                'index'     =>'template_id',
                'sortable' =>false,
                'filter'   => false,
                'no_link' => true,
                'width'	   => '170px',
                'renderer' => 'Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action'
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

}

