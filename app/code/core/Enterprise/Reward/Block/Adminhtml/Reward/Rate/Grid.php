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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward rate grid
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rewardRatesGrid');
//        $this->setDefaultSort('created_at');
//        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Enterprise_Reward_Model_Mysql4_Reward_Rate_Collection */
        $collection = Mage::getModel('enterprise_reward/reward_rate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rate_id', array(
            'header'    => Mage::helper('enterprise_reward')->__('ID'),
            'align'     => 'left',
            'index'     => 'rate_id',
        ));

        $this->addColumn('website_id', array(
            'header'  => Mage::helper('enterprise_reward')->__('Website'),
            'index'   => 'website_id',
            'type'    => 'options',
            'options' => Mage::getModel('enterprise_reward/source_website')->toOptionArray()
        ));

        $this->addColumn('customer_group_id', array(
            'header'  => Mage::helper('enterprise_reward')->__('Customer Group'),
            'index'   => 'customer_group_id',
            'type'    => 'options',
            'options' => Mage::getModel('enterprise_reward/source_customer_groups')->toOptionArray()
        ));

        $this->addColumn('direction', array(
            'header'  => Mage::helper('enterprise_reward')->__('Direction'),
            'index'   => 'direction',
            'type'    => 'options',
            'options' => Mage::getModel('enterprise_reward/reward_rate')->getDirectionsOptionArray()
        ));

        $this->addColumn('rate_description', array(
            'header'  => Mage::helper('enterprise_reward')->__('Rate Description'),
            'getter' => 'getExchangeRateAsText',
//            'renderer' => 'enterprise_reward/adminhtml_widget_grid_column_renderer_rate',
        ));

//        $this->addColumn('currency_to_points', array(
//            'header'  => Mage::helper('enterprise_reward')->__('Convert Currency to Points Rate'),
//            'renderer' => 'enterprise_reward/adminhtml_widget_grid_column_renderer_rate',
//        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('rate_id' => $row->getId()));
    }
}
