<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry entity items resource model
 */
class Enterprise_GiftRegistry_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('enterprise_giftregistry/item', 'item_id');
    }

    /**
     * Load item by registry id and product id
     *
     * @param Enterprise_GiftRegistry_Model_Item $object
     * @param int $registryId
     * @param int $productId
     * @return Enterprise_GiftRegistry_Model_Mysql4_Item
     */
    public function loadByProductRegistry($object, $registryId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('entity_id=?', $registryId)
            ->where('product_id=?', $productId);

        if ($data = $adapter->fetchRow($select)) {
            $object->setData($data);
        }

        $this->_afterLoad($object);
        return $this;
    }
}
