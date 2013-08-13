<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collection of banner <-> sales rule associations
 */
class Enterprise_Banner_Model_Resource_Salesrule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'enterprise_banner_salesrule_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * Define collection item type and corresponding table
     */
    protected function _construct()
    {
        $this->_init('Magento_Object', 'Mage_SalesRule_Model_Resource_Rule');
        $this->setMainTable('enterprise_banner_salesrule');
    }

    /**
     * Filter out disabled banners
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                array('banner' => $this->getTable('enterprise_banner')),
                'banner.banner_id = main_table.banner_id AND banner.is_enabled = 1',
                array()
            )
            ->group('main_table.banner_id')
        ;
        return $this;
    }

    /**
     * Add sales rule ids filter to the collection
     *
     * @param array $ruleIds
     * @return Enterprise_Banner_Model_Resource_Salesrule_Collection
     */
    public function addRuleIdsFilter(array $ruleIds)
    {
        if (!$ruleIds) {
            // force to match no rules
            $ruleIds = array(0);
        }
        $this->addFieldToFilter('main_table.rule_id', array('in' => $ruleIds));
        return $this;
    }
}
