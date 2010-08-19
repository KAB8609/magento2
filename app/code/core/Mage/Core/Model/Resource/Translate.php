<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Translation resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Translate extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }

    /**
     * Retrieve translation array for store / locale code
     *
     * @param int $storeId
     * @param string $locale
     * @return array
     */
    public function getTranslationArray($storeId = null, $locale = null)
    {
        if (!Mage::isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        
        $read = $this->_getReadAdapter();
        if (!$read) {
            return array();
        }

        $select = $read->select()
            ->from($this->getMainTable())
            ->where('store_id IN (:store_id)')
            ->where('locale=:locale')
            ->order('store_id');
        
        $bind = array(
            'locale'   => $locale,
            'store_id' => implode(',', array(0, $storeId))
        );

        return $read->fetchPairs($select, $bind);

    }

    /**
     * Retrieve translations array by strings
     *
     * @param array $strings
     * @param int_type $storeId
     * @return array
     */
    public function getTranslationArrayByStrings(array $strings, $storeId = null)
    {
        if (!Mage::isInstalled()) {
            return array();
        }

        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $read = $this->_getReadAdapter();
        if (!$read) {
            return array();
        }

        if (empty($strings)) {
            return array();
        }
 
        $bind = array(
            'tr_strings' => $strings,
            'store_id'   => $storeId
        );
        $select = $read->select()
            ->from($this->getMainTable())
            ->where('string IN (:tr_strings)')
            ->where('store_id = :store_id');

        return $read->fetchPairs($select, $bind);
    }

    /**
     * Retrieve table checksum
     *
     * @return int
     */
    public function getMainChecksum()
    {
        return $this->getChecksum($this->getMainTable());
    }
}
