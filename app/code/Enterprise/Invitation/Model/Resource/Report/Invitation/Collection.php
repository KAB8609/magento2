<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports invitation report collection
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Invitation_Model_Resource_Report_Invitation_Collection
    extends Enterprise_Invitation_Model_Resource_Invitation_Collection
{
    /**
     * Joins Invitation report data, and filter by date
     *
     * @param Zend_Date|string $fromDate
     * @param Zend_Date|string $toDate
     * @return Enterprise_Invitation_Model_Resource_Report_Invitation_Collection
     */
    public function setDateRange($fromDate, $toDate)
    {
        $this->_reset();

        $canceledField = $this->getConnection()->getCheckSql(
            'main_table.status = '
                . $this->getConnection()->quote(Enterprise_Invitation_Model_Invitation::STATUS_CANCELED),
            '1', '0'
        );

        $canceledRate = $this->getConnection()->getCheckSql(
            'COUNT(main_table.invitation_id) = 0',
            '0',
            'SUM(' . $canceledField . ') / COUNT(main_table.invitation_id) * 100'
        );

        $acceptedRate = $this->getConnection()->getCheckSql(
            'COUNT(main_table.invitation_id) = 0',
            '0',
            'COUNT(DISTINCT main_table.referral_id) / COUNT(main_table.invitation_id) * 100'
        );

        $this->addFieldToFilter('invitation_date', array('from' => $fromDate, 'to' => $toDate, 'time' => true))
            ->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'sent' => new Zend_Db_Expr('COUNT(main_table.invitation_id)'),
                'accepted' => new Zend_Db_Expr('COUNT(DISTINCT main_table.referral_id)'),
                'canceled' => new Zend_Db_Expr('SUM(' . $canceledField . ') '),
                'canceled_rate' => $canceledRate,
                'accepted_rate' => $acceptedRate
            ));

        $this->_joinFields($fromDate, $toDate);

        return $this;
    }

    /**
     * Join custom fields
     *
     * @return Enterprise_Invitation_Model_Resource_Report_Invitation_Collection
     */
    protected function _joinFields()
    {
        return $this;
    }

    /**
     * Filters report by stores
     *
     * @param array $storeIds
     * @return Enterprise_Invitation_Model_Resource_Report_Invitation_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addFieldToFilter('main_table.store_id', array('in' => (array)$storeIds));
        }
        return $this;
    }
}
