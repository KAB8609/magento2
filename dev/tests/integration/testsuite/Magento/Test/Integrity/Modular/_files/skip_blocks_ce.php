<?php
/**
 * List of blocks to be skipped from instantiation test
 *
 * Format: array('Block_Class_Name', ...)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    // Blocks with abstract constructor arguments
    'Magento\Backend\Block\System\Email\Template',
    'Magento\Backend\Block\System\Email\Template\Edit',
    'Magento\Backend\Block\System\Config\Edit',
    'Magento\Backend\Block\System\Config\Form',
    'Magento\Backend\Block\System\Config\Tabs',
    'Magento\Review\Block\Form',
    // Fails because of bug in \Magento\Webapi\Model\Acl\Loader\Resource\ConfigReader constructor
    'Magento\Cms\Block\Adminhtml\Page',
    'Magento\Cms\Block\Adminhtml\Page\Edit',
    'Magento\Sales\Block\Adminhtml\Order',
    'Magento\Oauth\Block\Adminhtml\Oauth\Consumer',
    'Magento\Oauth\Block\Adminhtml\Oauth\Consumer\Grid',
    'Magento\Paypal\Block\Adminhtml\Settlement\Report',
    'Magento\Sales\Block\Adminhtml\Billing\Agreement\View',
    'Magento\User\Block\Role\Tab\Edit',
    'Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource',
    // Fails because of dependence on registry
    'Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers',
);
