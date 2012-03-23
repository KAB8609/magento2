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
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $reviewStatus Mage_Review_Model_Review_Status */
$reviewStatus = Mage::getModel('review/review_status')->load(Mage_Review_Model_Review::STATUS_APPROVED);

return array(
    'product_id' => '__PLACEHOLDER__',
    'status' => $reviewStatus->getStatusCode(),
    'status_id' => Mage_Review_Model_Review::STATUS_APPROVED,
    'stores' => 'INVALID STORES',
    'nickname' => 'Test Nickname',
    'title' => 'Test Summary',
    'detail' => 'Test Review',
);
