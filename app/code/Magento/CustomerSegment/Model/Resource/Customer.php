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
 * Enterprise CustomerSegment Customer Resource Model
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource;

class Customer extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\App\Resource $resource
     */
    public function __construct(\Magento\Stdlib\DateTime $dateTime, \Magento\App\Resource $resource)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Intialize resource model
     */
    protected function _construct()
    {
        $this->_init('magento_customersegment_customer', 'customer_id');
    }

    /**
     * Save relations between customer id and segment ids with specific website id
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Resource\Customer
     */
    public function addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $now = $this->dateTime->formatDate(time(), true);
        foreach ($segmentIds as $segmentId) {
            $data = array(
                'segment_id'    => $segmentId,
                'customer_id'   => $customerId,
                'added_date'    => $now,
                'updated_date'  => $now,
                'website_id'    => $websiteId,
            );
            $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $data, array('updated_date'));
        }
        return $this;
    }

    /**
     * Remove relations between customer id and segment ids on specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return \Magento\CustomerSegment\Model\Resource\Customer
     */
    public function removeCustomerFromWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        if (!empty($segmentIds)) {
            $this->_getWriteAdapter()->delete($this->getMainTable(), array(
                'customer_id=?'     => $customerId,
                'website_id=?'      => $websiteId,
                'segment_id IN(?)'  => $segmentIds
            ));
        }
        return $this;
    }

    /**
     * Get segment ids assigned to customer id on specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function getCustomerWebsiteSegments($customerId, $websiteId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('c' => $this->getMainTable()), 'segment_id')
            ->join(
                array('s' => $this->getTable('magento_customersegment_segment')),
                'c.segment_id = s.segment_id'
            )
            ->where('is_active = 1')
            ->where('customer_id = :customer_id')
            ->where('website_id = :website_id');
        $bind = array(
            ':customer_id' => $customerId,
            ':website_id'  => $websiteId
        );
        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }
}
