<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TargetRule\Model\Actions\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\TargetRule\Model\Actions\Condition\Product\AttributesFactory
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\TargetRule\Model\Actions\Condition\Product\SpecialFactory
     */
    protected $_specialFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\TargetRule\Model\Actions\Condition\Product\AttributesFactory $attributeFactory
     * @param \Magento\TargetRule\Model\Actions\Condition\Product\SpecialFactory $specialFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\TargetRule\Model\Actions\Condition\Product\AttributesFactory $attributeFactory,
        \Magento\TargetRule\Model\Actions\Condition\Product\SpecialFactory $specialFactory,
        array $data = array()
    ) {
        $this->_attributeFactory = $attributeFactory;
        $this->_specialFactory = $specialFactory;
        parent::__construct($context, $data);
        $this->setType('Magento\TargetRule\Model\Actions\Condition\Combine');
    }

    /**
     * Prepare list of contitions
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('value'=>$this->getType(), 'label'=>__('Conditions Combination')),
            $this->_attributeFactory->create()->getNewChildSelectOptions(),
            $this->_specialFactory->create()->getNewChildSelectOptions(),
        );
        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param \Magento\TargetRule\Model\Index $object
     * @param array $bind
     * @return \Zend_Db_Expr
     */
    public function getConditionForCollection($collection, $object, &$bind)
    {
        $conditions = array();
        $aggregator = $this->getAggregator() == 'all' ? ' AND ' : ' OR ';
        $operator   = $this->getValue() ? '' : 'NOT';

        foreach ($this->getConditions() as $condition) {
            $subCondition = $condition->getConditionForCollection($collection, $object, $bind);
            if ($subCondition) {
                $conditions[] = sprintf('%s %s', $operator, $subCondition);
            }
        }

        if ($conditions) {
            return new \Zend_Db_Expr(sprintf('(%s)', join($aggregator, $conditions)));
        }

        return false;
    }
}

