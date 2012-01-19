<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reminder rules resource collection model
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reminder_Model_Resource_Rule_Collection extends Mage_Rule_Model_Resource_Rule_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'website' => array(
            'associations_table' => 'enterprise_reminder/website',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'website_id'
        )
    );

    /**
     * Set resource model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_Reminder_Model_Rule', 'Enterprise_Reminder_Model_Resource_Rule');
        $this->addFilterToMap('rule_id', 'main_table.rule_id');
    }

    /**
     * Limit rules collection by date columns
     *
     * @param string $date
     *
     * @return Enterprise_Reminder_Model_Resource_Rule_Collection
     */
    public function addDateFilter($date)
    {
        $this->getSelect()
            ->where('date_from IS NULL OR date_from <= ?', $date)
            ->where('date_to IS NULL OR date_to >= ?', $date);

        return $this;
    }

    /**
     * Limit rules collection by separate rule
     *
     * @param int $value
     * @return Enterprise_Reminder_Model_Resource_Rule_Collection
     */
    public function addRuleFilter($value)
    {
        $this->getSelect()->where('main_table.rule_id = ?', $value);
        return $this;
    }
}
