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
 * Sales conditions combine
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Sales;

class Combine
    extends \Magento\CustomerSegment\Model\Condition\Combine\AbstractCombine
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = array()
    ) {
        parent::__construct($conditionFactory, $resourceSegment, $context, $data);
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Sales\Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array_merge_recursive(parent::getNewChildSelectOptions(), array(
            $this->_conditionFactory->create('Order_Status')->getNewChildSelectOptions(),
            // date ranges
            array(
                'value' => array(
                    $this->_conditionFactory->create('Uptodate')->getNewChildSelectOptions(),
                    $this->_conditionFactory->create('Daterange')->getNewChildSelectOptions(),
                ),
                'label' => __('Date Ranges'),
            ),
        ));
    }

    /**
     * Init attribute select options
     *
     * @return \Magento\CustomerSegment\Model\Segment\Condition\Sales\Combine
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'total'   => __('Total'),
            'average' => __('Average'),
        ));
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Check if validation should be strict
     *
     * @return bool
     */
    protected function _getRequiredValidation()
    {
        return true;
    }

    /**
     * Get field names map for subfilters
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array(
            'order' => 'sales_order.status',
            'date' => 'sales_order.created_at',
        );
    }
}
