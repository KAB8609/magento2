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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard sales statistics bar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author	   Dmytro Vasylenko <dmitriy.vasilenko@varien.com>
 */

class Mage_Adminhtml_Block_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/salebar.phtml');


    }

    protected function _prepareLayout()
    {
        $collection = Mage::getResourceModel('reports/order_collection')
            ->calculateSales($this->getRequest()->getParam('store'));

        if ($this->getRequest()->getParam('store')) {
            $collection->addAttributeToFilter('store_id', $this->getRequest()->getParam('store'));
        }

        $collection->load();
        $collectionArray = $collection->toArray();
        $sales = array_pop($collectionArray);

        $this->addTotal($this->__('Lifetime Sales'), $sales['lifetime']);
        $this->addTotal($this->__('Average Sales'), $sales['average']);
    }
}
