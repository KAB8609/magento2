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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Helper_Mysql4 extends Mage_Sales_Model_Resource_Helper_Abstract
{
    /**
     * Update rating position
     *
     * @param string $aggregationTableName
     * @return Mage_Sales_Model_Resource_Report_Bestsellers
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationTable)
    {
        $adapter = $this->_getWriteAdapter();
        $periodSubSelect = $adapter->select();
        $ratingSubSelect = $adapter->select();
        $ratingSelect = $adapter->select();

        $periodCol = 't.period';
        if ($aggregation == $this->_aggregationAliases['monthly']) {
            $periodCol = $adapter->getDateFormatSql('t.period', '%Y-%m-01');
        } else if ($aggregation == $this->_aggregationAliases['yearly']) {
            $periodCol = $adapter->getDateFormatSql('t.period', '%Y-01-01');
        }

        $columns = array(
            'period'        => 't.period',
            'store_id'      => 't.store_id',
            'product_id'    => 't.product_id',
            'product_name'  => 't.product_name',
            'product_price' => 't.product_price',
        );

        if ($aggregation == $this->_aggregationAliases['daily']) {
            $columns['id'] = 't.id';  // to speed-up insert on duplicate key update
        }

        $cols = array_keys($columns);
        $cols[] = new Zend_Db_Expr('SUM(t.qty_ordered) AS total_qty_ordered');
        $periodSubSelect->from(array('t' => $this->_mainTableName), $cols)
            ->group(array('t.store_id', $periodCol, 't.product_id'))
            ->order(array('t.store_id', $periodCol, 'total_qty_ordered DESC'));

        $cols = $columns;
        $cols['qty_ordered'] = 't.total_qty_ordered';
        $cols['rating_pos']  = new Zend_Db_Expr("(@pos := IF(t.`store_id` <> @prevStoreId OR {$periodCol} <> @prevPeriod, 1, @pos+1))");
        $cols['prevStoreId'] = new Zend_Db_Expr('(@prevStoreId := t.`store_id`)');
        $cols['prevPeriod']  = new Zend_Db_Expr("(@prevPeriod := {$periodCol})");
        $ratingSubSelect->from($periodSubSelect, $cols);

        $cols = $columns;
        $cols['period']      = $periodCol;  // important!
        $cols['qty_ordered'] = 't.qty_ordered';
        $cols['rating_pos']  = 't.rating_pos';
        $ratingSelect->from($ratingSubSelect, $cols);

        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols), false);
        $this->_getWriteAdapter()->query("SET @pos = 0, @prevStoreId = -1, @prevPeriod = '0000-00-00'");
        
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }

}
