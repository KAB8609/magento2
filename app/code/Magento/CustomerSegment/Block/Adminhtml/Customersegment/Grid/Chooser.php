<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment grid
 *
 * @category   Magento
 * @package    Magento_CustomerSegment
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment\Grid;

class Chooser
    extends \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Grid
{
    /**
     * Intialize grid
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        } else {
            $this->setId('customersegment_grid_chooser_'.$this->getId());
        }

        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);

        $form = $this->getRequest()->getParam('form');
        if ($form) {
            $this->setRowClickCallback("$form.chooserGridRowClick.bind($form)");
            $this->setCheckboxCheckCallback("$form.chooserGridCheckboxCheck.bind($form)");
            $this->setRowInitCallback("$form.chooserGridRowInit.bind($form)");
        }
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    /**
     * Row click javasctipt callback getter
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return $this->_getData('row_click_callback');
    }

    /**
     * Prepare columns for grid
     *
     * @return \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Grid\Chooser
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_segments', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_segments',
            'values'    => $this->_getSelectedSegments(),
            'align'     => 'center',
            'index'     => 'segment_id',
            'use_index' => true,
        ));
        return parent::_prepareColumns();
    }

    /**
     * Get Selected ids param from request
     *
     * @return array
     */
    protected function _getSelectedSegments()
    {
        $segments = $this->getRequest()->getPost('selected', array());
        return $segments;
    }

    /**
     * Grid URL getter for ajax mode
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customersegment/index/chooserGrid', array('_current' => true));
    }
}
