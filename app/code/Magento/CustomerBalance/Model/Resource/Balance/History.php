<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customerbalance history resource model
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerBalance\Model\Resource\Balance;

class History extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('magento_customerbalance_history', 'history_id');
    }

    /**
     * Set updated_at automatically before saving
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\CustomerBalance\Model\Resource\Balance\History
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Mark specified balance history record as sent to customer
     *
     * @param int $id
     */
    public function markAsSent($id)
    {
        $this->_getWriteAdapter()->update($this->getMainTable(),
            array('is_customer_notified' => 1),
            array('history_id = ?' => $id)
        );
    }
}
