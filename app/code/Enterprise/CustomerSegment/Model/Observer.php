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
 * CustomerSegment observer
 *
 */
class Enterprise_CustomerSegment_Model_Observer
{
    /**
     * @var Enterprise_CustomerSegment_Helper_Data
     */
    private $_segmentHelper;

    /**
     * @param Enterprise_CustomerSegment_Helper_Data $segmentHelper
     */
    public function __construct(Enterprise_CustomerSegment_Helper_Data $segmentHelper)
    {
        $this->_segmentHelper = $segmentHelper;
    }

    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param Magento_Event_Observer $observer
     */
    public function addSegmentsToSalesRuleCombine(Magento_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions(array(array(
            'label' => __('Customer Segment'),
            'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Segment'
        )));
    }

    /**
     * Process customer related data changing. Method can process just events with customer object
     *
     * @param   Magento_Event_Observer $observer
     */
    public function processCustomerEvent(Magento_Event_Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $customer  = $observer->getEvent()->getCustomer();
        $dataObject= $observer->getEvent()->getDataObject();
        $customerId= false;

        if ($customer) {
            $customerId = $customer->getId();
        }
        if (!$customerId && $dataObject) {
            $customerId = $dataObject->getCustomerId();
        }

        if ($customerId) {
            Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processCustomerEvent(
                $eventName,
                $customerId
            );
        }
    }

    /**
     * Match customer segments on supplied event for currently logged in customer or visitor and current website.
     * Can be used for processing just frontend events
     *
     * @param Magento_Event_Observer $observer
     */
    public function processEvent(Magento_Event_Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $customer = Mage::registry('segment_customer');

        // For visitors use customer instance from customer session
        if (!$customer) {
            $customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
        }

        $website = Mage::app()->getStore()->getWebsite();
        Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processEvent($eventName, $customer, $website);
    }

    /**
     * Match quote customer to all customer segments.
     * Used before quote recollect in admin
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function processQuote(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();
        if ($customer && $customer->getId()) {
            $website = $quote->getStore()->getWebsite();
            Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processCustomer($customer, $website);
        }
    }

    /**
     * Add field "Use in Customer Segment" for Customer and Customer Address attribute edit form
     *
     * @param Magento_Event_Observer $observer
     */
    public function enterpiseCustomerAttributeEditPrepareForm(Magento_Event_Observer $observer)
    {
        $form       = $observer->getEvent()->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        $fieldset->addField('is_used_for_customer_segment', 'select', array(
            'name'      => 'is_used_for_customer_segment',
            'label'     => __('Use in Customer Segment'),
            'title'     => __('Use in Customer Segment'),
            'values'    => Mage::getModel('Mage_Backend_Model_Config_Source_Yesno')->toOptionArray(),
        ));
    }

    /**
     * Add Customer Segment form fields to Target Rule form
     *
     * Observe  targetrule_edit_tab_main_after_prepare_form event
     *
     * @param Magento_Event_Observer $observer
     */
    public function addFieldsToTargetRuleForm(Magento_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var $form Magento_Data_Form */
        $form = $observer->getEvent()->getForm();
        /** @var Magento_Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getEvent()->getBlock();

        /** @var Mage_Backend_Block_Widget_Form_Element_Dependence $fieldDependencies */
        $fieldDependencies = $block->getLayout()->createBlock('Mage_Backend_Block_Widget_Form_Element_Dependence');
        $block->setChild('form_after', $fieldDependencies);

        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $fieldDependencies);
    }
}
