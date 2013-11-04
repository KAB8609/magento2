<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity items resource model
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Resource;

class Item extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(\Magento\Stdlib\DateTime $dateTime, \Magento\Core\Model\Resource $resource)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Resource model initialization
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_item', 'item_id');
    }

    /**
     * Add creation date to object
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        if (!$object->getAddedAt()) {
            $object->setAddedAt($this->dateTime->formatDate(true));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Load item by registry id and product id
     *
     * @param \Magento\GiftRegistry\Model\Item $object
     * @param int $registryId
     * @param int $productId
     * @return \Magento\GiftRegistry\Model\Resource\Item
     */
    public function loadByProductRegistry($object, $registryId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('entity_id = :entity_id')
            ->where('product_id = :product_id');
        $bind = array(
            ':entity_id'  => (int)$registryId,
            ':product_id' => (int)$productId
        );
        $data = $adapter->fetchRow($select, $bind);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);
        return $this;
    }
}
