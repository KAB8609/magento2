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
 * @package     Enterprise_GiftCardAccount
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Enter description here ...
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_GiftCardAccount_Model_Resource_Pool_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Delete records in db using specified status as criteria
     *
     * @param int $status
     * @return Enterprise_GiftCardAccount_Model_Resource_Pool_Abstract
     */
    public function cleanupByStatus($status)
    {
        $where = array('status = ?' => $status);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }
}
