<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $eventClosed Enterprise_CatalogEvent_Model_Event */
$eventClosed = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
$eventClosed
    ->setCategoryId(null)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 year')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDisplayState(Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE)
    ->setSortOrder(30)
    ->save()
;

/** @var $eventOpen Enterprise_CatalogEvent_Model_Event */
$eventOpen = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
$eventOpen
    ->setCategoryId(1)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDisplayState(Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE)
    ->setSortOrder(20)
    ->save()
;

/** @var $eventUpcoming Enterprise_CatalogEvent_Model_Event */
$eventUpcoming = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
$eventUpcoming
    ->setCategoryId(2)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 year')))
    ->setDisplayState(
        Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE
        | Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE
    )
    ->setSortOrder(10)
    ->save()
;
