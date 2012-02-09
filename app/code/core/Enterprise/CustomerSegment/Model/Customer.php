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
 * Segment/customer relatio model. Model working in website scope. If website is not declared
 * all methods are working in current ran website scoupe
 *
 * @method Enterprise_CustomerSegment_Model_Resource_Customer _getResource()
 * @method Enterprise_CustomerSegment_Model_Resource_Customer getResource()
 * @method int getSegmentId()
 * @method Enterprise_CustomerSegment_Model_Customer setSegmentId(int $value)
 * @method int getCustomerId()
 * @method Enterprise_CustomerSegment_Model_Customer setCustomerId(int $value)
 * @method string getAddedDate()
 * @method Enterprise_CustomerSegment_Model_Customer setAddedDate(string $value)
 * @method string getUpdatedDate()
 * @method Enterprise_CustomerSegment_Model_Customer setUpdatedDate(string $value)
 * @method int getWebsiteId()
 * @method Enterprise_CustomerSegment_Model_Customer setWebsiteId(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Array of Segments collections per event name
     *
     * @var array
     */
    protected $_segmentMap = array();

    /**
     * Array of segment ids per customer id and website id
     *
     * @var array
     */
    protected $_customerWebsiteSegments = array();

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Enterprise_CustomerSegment_Model_Resource_Customer');
    }

    /**
     * Get list of active segments for specific event
     *
     * @param string $eventName
     * @param int $websiteId
     * @return Enterprise_CustomerSegment_Model_Resource_Segment_Collection
     */
    public function getActiveSegmentsForEvent($eventName, $websiteId)
    {
        if (!isset($this->_segmentMap[$eventName][$websiteId])) {
            $relatedSegments = Mage::getResourceModel('Enterprise_CustomerSegment_Model_Resource_Segment_Collection')
                ->addEventFilter($eventName)
                ->addWebsiteFilter($websiteId)
                ->addIsActiveFilter(1);
            $this->_segmentMap[$eventName][$websiteId] = $relatedSegments;
        }
        return $this->_segmentMap[$eventName][$websiteId];
    }

    /**
     * Match all related to event segments and assign/deassign customer/visitor to segments on specific website
     *
     * @param   string $eventName
     * @param   Mage_Customer_Model_Customer | int $customer
     * @param   Mage_Core_Model_Website | int $website
     * @return  Enterprise_CustomerSegment_Model_Customer
     */
    public function processEvent($eventName, $customer, $website)
    {
        Magento_Profiler::start('__SEGMENTS_MATCHING__');
        $website = Mage::app()->getWebsite($website);
        $segments = $this->getActiveSegmentsForEvent($eventName, $website->getId());

        $this->_processSegmentsValidation($customer, $website, $segments);

        Magento_Profiler::stop('__SEGMENTS_MATCHING__');
        return $this;
    }

    /**
     * Validate all segments for specific customer/visitor on specific website
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @param   Mage_Core_Model_Website $website
     * @return  Enterprise_CustomerSegment_Model_Customer
     */
    public function processCustomer(Mage_Customer_Model_Customer $customer, $website)
    {
        $website = Mage::app()->getWebsite($website);
        $segments = Mage::getResourceModel('Enterprise_CustomerSegment_Model_Resource_Segment_Collection')
            ->addWebsiteFilter($website)
            ->addIsActiveFilter(1);

        $this->_processSegmentsValidation($customer, $website, $segments);

        return $this;
    }

    /**
     * Check if customer is related to segments and update customer-segment relations
     *
     * @param int|null|Mage_Customer_Model_Customer $customer
     * @param Mage_Core_Model_Website $website
     * @param Enterprise_CustomerSegment_Model_Resource_Segment_Collection $segments
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    protected function _processSegmentsValidation($customer, $website, $segments)
    {
        $websiteId = $website->getId();
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customerId = $customer->getId();
        } else {
            $customerId = $customer;
        }

        $matchedIds = array();
        $notMatchedIds = array();
        $useVisitorId = !$customer || !$customerId;
        foreach ($segments as $segment) {
            if ($useVisitorId) {
                // Skip segment if it cannot be applied to visitor
                if ($segment->getApplyTo() == Enterprise_CustomerSegment_Model_Segment::APPLY_TO_REGISTERED) {
                    continue;
                }
                $segment->setVisitorId(Mage::getSingleton('Mage_Log_Model_Visitor')->getId());
            } else {
                // Skip segment if it cannot be applied to customer
                if ($segment->getApplyTo() == Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                    continue;
                }
            }
            $isMatched = $segment->validateCustomer($customer, $website);
            if ($isMatched) {
                $matchedIds[]   = $segment->getId();
            } else {
                $notMatchedIds[]= $segment->getId();
            }
        }


        if ($customerId) {
            $this->addCustomerToWebsiteSegments($customerId, $websiteId, $matchedIds);
            $this->removeCustomerFromWebsiteSegments($customerId, $websiteId, $notMatchedIds);
            $segmentIds = $this->_customerWebsiteSegments[$websiteId][$customerId];
        } else {
            $this->addVisitorToWebsiteSegments(Mage::getSingleton('customer/session'), $websiteId, $matchedIds);
            $this->removeVisitorFromWebsiteSegments(Mage::getSingleton('customer/session'), $websiteId, $notMatchedIds);
            $allSegments= Mage::getSingleton('customer/session')->getCustomerSegmentIds();
            $segmentIds = $allSegments[$websiteId];
        }

        Mage::dispatchEvent('enterprise_customersegment_ids_changed', array('segment_ids' => $segmentIds));

        return $this;
    }

    /**
     * Match customer id to all segments related to event on all websites where customer can be presented
     *
     * @param string $eventName
     * @param int $customerId
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function processCustomerEvent($eventName, $customerId)
    {
        if (Mage::getSingleton('Mage_Customer_Model_Config_Share')->isWebsiteScope()) {
            $websiteIds = Mage::getResourceSingleton('Mage_Customer_Model_Resource_Customer')
                ->getWebsiteId($customerId);

            if ($websiteIds) {
                $websiteIds = array($websiteIds);
            } else {
                $websiteIds = array();
            }
        } else {
            $websiteIds = Mage::app()->getWebsites();
            $websiteIds = array_keys($websiteIds);
        }
        foreach ($websiteIds as $websiteId) {
            $this->processEvent($eventName, $customerId, $websiteId);
        }
        return $this;
    }

    /**
     * Add visitor-segment relation for specified website
     *
     * @param Mage_Core_Model_Session_Abstract $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function addVisitorToWebsiteSegments($visitorSession, $websiteId, $segmentIds)
    {
        $visitorSegmentIds = $visitorSession->getCustomerSegmentIds();
        if (!is_array($visitorSegmentIds)) {
            $visitorSegmentIds = array();
        }
        if (isset($visitorSegmentIds[$websiteId]) && is_array($visitorSegmentIds[$websiteId])) {
            $segmentsIdsForWebsite = $visitorSegmentIds[$websiteId];
            if (!empty($segmentIds)) {
                $segmentsIdsForWebsite = array_unique(array_merge($segmentsIdsForWebsite, $segmentIds));
            }
            $visitorSegmentIds[$websiteId] = $segmentsIdsForWebsite;
        } else {
            $visitorSegmentIds[$websiteId] = $segmentIds;
        }
        $visitorSession->setCustomerSegmentIds($visitorSegmentIds);
        return $this;
    }

    /**
     * Remove visitor-segment relation for specified website
     *
     * @param Mage_Core_Model_Session_Abstract $visitorSession
     * @param int $websiteId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function removeVisitorFromWebsiteSegments($visitorSession, $websiteId, $segmentIds)
    {
        $visitorCustomerSegmentIds = $visitorSession->getCustomerSegmentIds();
        if (!is_array($visitorCustomerSegmentIds)) {
            $visitorCustomerSegmentIds = array();
        }
        if (isset($visitorCustomerSegmentIds[$websiteId]) && is_array($visitorCustomerSegmentIds[$websiteId])) {
            $segmentsIdsForWebsite = $visitorCustomerSegmentIds[$websiteId];
            if (!empty($segmentIds)) {
                $segmentsIdsForWebsite = array_diff($segmentsIdsForWebsite, $segmentIds);
            }
            $visitorCustomerSegmentIds[$websiteId] = $segmentsIdsForWebsite;
        }
        $visitorSession->setCustomerSegmentIds($visitorCustomerSegmentIds);
        return $this;
    }

    /**
     * Add customer relation with segment for specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $existingIds = $this->getCustomerSegmentIdsForWebsite($customerId, $websiteId);
        $this->_getResource()->addCustomerToWebsiteSegments($customerId, $websiteId, $segmentIds);
        $this->_customerWebsiteSegments[$websiteId][$customerId] = array_unique(array_merge($existingIds, $segmentIds));
        return $this;
    }

    /**
     * Remove customer id association with segment ids on specific website
     *
     * @param int $customerId
     * @param int $websiteId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Customer
     */
    public function removeCustomerFromWebsiteSegments($customerId, $websiteId, $segmentIds)
    {
        $existingIds = $this->getCustomerSegmentIdsForWebsite($customerId, $websiteId);
        $this->_getResource()->removeCustomerFromWebsiteSegments($customerId, $websiteId, $segmentIds);
        $this->_customerWebsiteSegments[$websiteId][$customerId] = array_diff($existingIds, $segmentIds);
        return $this;
    }

    /**
     * Get segment ids for specific customer id and website id
     *
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function getCustomerSegmentIdsForWebsite($customerId, $websiteId)
    {
        if (!isset($this->_customerWebsiteSegments[$websiteId][$customerId])) {
            $this->_customerWebsiteSegments[$websiteId][$customerId] = $this->_getResource()
                ->getCustomerWebsiteSegments($customerId, $websiteId);
        }
        return $this->_customerWebsiteSegments[$websiteId][$customerId];
    }
}
