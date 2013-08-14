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
 * Customer newsletter subscription
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Newsletter
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Newsletter');
        $this->setValue(1);
    }

    /**
     * Set data with filtering
     *
     * @param mixed $key
     * @param mixed $value
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Newsletter
     */
    public function setData($key, $value = null)
    {
        //filter key "value"
        if (is_array($key) && isset($key['value']) && $key['value'] !== null) {
            $key['value'] = (int) $key['value'];
        } elseif ($key == 'value' && $value !== null) {
            $value = (int) $value;
        }

        return parent::setData($key, $value);
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('customer_save_commit_after', 'newsletter_subscriber_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(array(
            'value' => $this->getType(),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Newsletter Subscription')
         ));
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        $element = $this->getValueElementHtml();
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer is %s to newsletter.', $element)
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get element type for value select
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Init list of available values
     *
     * @return array
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            '1'  => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('subscribed'),
            '0' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('not subscribed'),
        ));
        return $this;
    }

    /**
     * Get condition query for customer balance
     *
     * @param $customer
     * @param int|Zend_Db_Expr $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        $table = $this->getResource()->getTable('newsletter_subscriber');
        $value = (int)$this->getValue();

        $select = $this->getResource()->createSelect()
            ->from(array('main' => $table), array(new Zend_Db_Expr($value)))
            ->where($this->_createCustomerFilter($customer, 'main.customer_id'))
            ->where('main.subscriber_status = ?', Magento_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
        $select->limit(1);
        $this->_limitByStoreWebsite($select, $website, 'main.store_id');
        if (!$value) {
            $select = $this->getResource()->getReadConnection()->getIfNullSql($select, 1);
        }
        return $select;
    }
}
