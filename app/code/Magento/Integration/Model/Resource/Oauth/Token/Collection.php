<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth token resource collection model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Integration\Model\Resource\Oauth\Token;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Integration\Model\Oauth\Token', 'Magento\Integration\Model\Resource\Oauth\Token');
    }

    /**
     * Load collection with consumer data
     *
     * Method use for show applications list (token-consumer)
     *
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function joinConsumerAsApplication()
    {
        $select = $this->getSelect();
        $select->joinLeft(
                    array('c' => $this->getTable('oauth_consumer')),
                    'c.entity_id = main_table.consumer_id',
                    'name'
                );

        return $this;
    }

    /**
     * Add filter by admin ID
     *
     * @param int $adminId
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterByAdminId($adminId)
    {
        $this->addFilter('main_table.admin_id', $adminId);
        return $this;
    }

    /**
     * Add filter by customer ID
     *
     * @param int $customerId
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterByCustomerId($customerId)
    {
        $this->addFilter('main_table.customer_id', $customerId);
        return $this;
    }

    /**
     * Add filter by consumer ID
     *
     * @param int $consumerId
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterByConsumerId($consumerId)
    {
        $this->addFilter('main_table.consumer_id', $consumerId);
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string $type
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterByType($type)
    {
        $this->addFilter('main_table.type', $type);
        return $this;
    }

    /**
     * Add filter by ID
     *
     * @param array|int $tokenId
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterById($tokenId)
    {
        $this->addFilter('main_table.entity_id', array('in' => $tokenId), 'public');
        return $this;
    }

    /**
     * Add filter by "Is Revoked" status
     *
     * @param bool|int $flag
     * @return \Magento\Integration\Model\Resource\Oauth\Token\Collection
     */
    public function addFilterByRevoked($flag)
    {
        $this->addFilter('main_table.revoked', (int) $flag, 'public');
        return $this;
    }
}