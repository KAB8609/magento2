<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_CustomerSegment_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * XPath where customer segment's on/off status is stored
     */
    const XML_PATH_CUSTOMER_SEGMENT_ENABLER = 'customer/enterprise_customersegment/is_enabled';

    /**
     * @var Magento_Core_Model_Store_Config
     */
    private $_storeConfig;

    /**
     * @var Enterprise_CustomerSegment_Model_Resource_Segment_Collection
     */
    private $_segmentCollection;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Enterprise_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $storeConfig,
        Enterprise_CustomerSegment_Model_Resource_Segment_Collection $segmentCollection
    ) {
        parent::__construct($context);
        $this->_storeConfig = $storeConfig;
        $this->_segmentCollection = $segmentCollection;
    }

    /**
     * Check whether customer segment functionality should be enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_storeConfig->getConfig(self::XML_PATH_CUSTOMER_SEGMENT_ENABLER);
    }

    /**
     * Retrieve options array for customer segment view mode
     *
     * @return array
     */
    public function getOptionsArray()
    {
        return array(
            array(
                'label' => '',
                'value' => ''
            ),
            array(
                'label' => $this->__('Union'),
                'value' => Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_UNION_CODE
            ),
            array(
                'label' => $this->__('Intersection'),
                'value' => Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_INTERSECT_CODE
            )
        );
    }

    /**
     * Return translated Label for option by specified option code
     *
     * @param string $code Option code
     * @return string
     */
    public function getViewModeLabel($code)
    {
        foreach ($this->getOptionsArray() as $option) {
            if (isset($option['label']) && isset($option['value']) && $option['value'] == $code) {
                return $option['label'];
            }
        }
        return '';
    }

    /**
     * Add customer segment fields to a form and its data
     *
     * @param Magento_Data_Form $form
     * @param Magento_Object $formData
     * @param Mage_Backend_Block_Widget_Form_Element_Dependence $fieldDependencies
     */
    public function addSegmentFieldsToForm(
        Magento_Data_Form $form,
        Magento_Object $formData,
        Mage_Backend_Block_Widget_Form_Element_Dependence $fieldDependencies
    ) {
        if (!$this->isEnabled()) {
            return;
        }

        $formData->setUseCustomerSegment(count($formData->getCustomerSegmentIds()) > 0);

        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField('use_customer_segment', 'select', array(
            'name' => 'use_customer_segment',
            'label' => $this->__('Customer Segments'),
            'options' => array(
                '0' => $this->__('All'),
                '1' => $this->__('Specified'),
            ),
            'note' => $formData->getUseCustomerSegment()
                ? $this->_getSpecificSegmentMessage()  : $this->_getAllSegmentsMessage(),
            'disabled' => $formData->getIsReadonly(),
            'after_element_html' => $this->_getChangeNoteMessageScript(
                "{$htmlIdPrefix}use_customer_segment",
                'use_customer_segment-note'
            )
        ));

        $fieldset->addField('customer_segment_ids', 'multiselect', array(
            'name' => 'customer_segment_ids',
            'values' => $this->_segmentCollection->toOptionArray(),
            'required' => true,
            'can_be_empty' => true,
        ));

        $fieldDependencies
            ->addFieldMap("{$htmlIdPrefix}use_customer_segment", 'use_customer_segment')
            ->addFieldMap("{$htmlIdPrefix}customer_segment_ids", 'customer_segment_ids')
            ->addFieldDependence('customer_segment_ids', 'use_customer_segment', '1');
    }

    /**
     * Retrieve JavaScript that actualizes customer segment field's note message upon changes in run-time
     *
     * @param string $selectBoxId
     * @param string $noteMessageBlockId
     * @return string
     */
    protected function _getChangeNoteMessageScript($selectBoxId, $noteMessageBlockId)
    {
        $allSegmentsMsg = $this->_getAllSegmentsMessage();
        $specificSegmentMsg = $this->_getSpecificSegmentMessage();
        return "<script type=\"text/javascript\">\r\n"
            . "(function($) {\r\n"
            . "'use strict';\r\n"
            . "var notes = [\"{$allSegmentsMsg}\", \"{$specificSegmentMsg}\"];\r\n"
            . "\$('#$selectBoxId').change(function() {\r\n"
            . "var note = notes[\$('#$selectBoxId').val()];\r\n"
            . "\$('#$noteMessageBlockId').html(note);\r\n"
            . "});\r\n"
            . "})(jQuery);\r\n"
            . "</script>\r\n";
    }

    /**
     * Retrieve translated "apply to all segments" message
     *
     * @return string
     */
    protected function _getAllSegmentsMessage()
    {
        return $this->__('Applies to All of the Specified Customer Segments');
    }

    /**
     * Retrieve translated "apply to specific segment" message
     *
     * @return string
     */
    protected function _getSpecificSegmentMessage()
    {
        return $this->__('Apply to the Selected Customer Segments');
    }
}
