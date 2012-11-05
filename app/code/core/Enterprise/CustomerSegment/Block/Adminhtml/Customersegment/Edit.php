<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for customer segment configuration
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Refresh Segment Data" button
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Enterprise_CustomerSegment';

        parent::_construct();

        /** @var $segment Enterprise_CustomerSegment_Model_Segment */
        $segment = Mage::registry('current_customer_segment');
        if ($segment && $segment->getId()) {
            $this->_addButton('match_customers', array(
                'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Refresh Segment Data'),
                'onclick'   => 'setLocation(\'' . $this->getMatchUrl() . '\')',
            ), -1);
        }

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 3);
    }

    /**
     * Get url for run segment customers matching
     *
     * @return string
     */
    public function getMatchUrl()
    {
        $segment = Mage::registry('current_customer_segment');
        return $this->getUrl('*/*/match', array('id'=>$segment->getId()));
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $segment = Mage::registry('current_customer_segment');
        if ($segment->getSegmentId()) {
            return Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__("Edit Segment '%s'", $this->escapeHtml($segment->getName()));
        }
        else {
            return Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('New Segment');
        }
    }
}
