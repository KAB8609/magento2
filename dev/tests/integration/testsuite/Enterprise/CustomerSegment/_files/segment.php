<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerSegment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$data = array(
    'name' => 'test',
    'is_active' => '1',
);
$segment = new Enterprise_CustomerSegment_Model_Segment;
$segment->loadPost($data);
$segment->save();