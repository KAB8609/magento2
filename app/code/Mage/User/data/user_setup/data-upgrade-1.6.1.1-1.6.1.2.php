<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$map = array(
    'admin/system/config/feed' => 'Find_Feed::config_feed',
    'admin/catalog/feed' => 'Find_Feed::feed',
    'admin/catalog/feed/import_items' => 'Find_Feed::import_items',
    'admin/catalog/feed/import_products' => 'Find_Feed::import_products',
    'admin/system/adminnotification' => 'Mage_AdminNotification::adminnotification',
    'admin/system/adminnotification/remove' => 'Mage_AdminNotification::adminnotification_remove',
    'admin/system/adminnotification/mark_as_read' => 'Mage_AdminNotification::mark_as_read',
    'admin/system/adminnotification/show_list' => 'Mage_AdminNotification::show_list',
    'admin/system/adminnotification/show_toolbar' => 'Mage_AdminNotification::show_toolbar',
    'admin' => 'Magento_Adminhtml::admin',
    'admin/system/config/advanced' => 'Magento_Adminhtml::advanced',
    'all' => 'Magento_Adminhtml::all',
    'admin/system/cache' => 'Magento_Adminhtml::cache',
    'admin/system/config' => 'Magento_Adminhtml::config',
    'admin/system/config/admin' => 'Magento_Adminhtml::config_admin',
    'admin/system/config/design' => 'Magento_Adminhtml::config_design',
    'admin/system/config/general' => 'Magento_Adminhtml::config_general',
    'admin/system/config/system' => 'Magento_Adminhtml::config_system',
    'admin/system/convert' => 'Magento_Adminhtml::convert',
    'admin/system/config/currency' => 'Magento_Adminhtml::currency',
    'admin/system/extensions/custom' => 'Magento_Adminhtml::custom',
    'admin/dashboard' => 'Magento_Adminhtml::dashboard',
    'admin/system/design' => 'Magento_Adminhtml::design',
    'admin/system/config/dev' => 'Magento_Adminhtml::dev',
    'admin/system/email_template' => 'Magento_Adminhtml::email_template',
    'admin/system/extensions' => 'Magento_Adminhtml::extensions',
    'admin/global_search' => 'Magento_Adminhtml::global_search',
    'admin/system/convert/gui' => 'Magento_Adminhtml::gui',
    'admin/system/extensions/local' => 'Magento_Adminhtml::local',
    'admin/system/myaccount' => 'Magento_Adminhtml::myaccount',
    'admin/system/convert/profiles' => 'Magento_Adminhtml::profiles',
    'admin/system/design/schedule' => 'Magento_Adminhtml::schedule',
    'admin/system/config/sendfriend' => 'Magento_Adminhtml::sendfriend',
    'admin/system/store' => 'Magento_Adminhtml::store',
    'admin/system' => 'Magento_Adminhtml::system',
    'admin/system/tools' => 'Magento_Adminhtml::tools',
    'admin/system/config/trans_email' => 'Magento_Adminhtml::trans_email',
    'admin/system/variable' => 'Magento_Adminhtml::variable',
    'admin/system/config/web' => 'Magento_Adminhtml::web',
    'admin/system/api' => 'Magento_Api::api',
    'admin/system/config/api' => 'Magento_Api::config_api',
    'admin/system/api/roles' => 'Magento_Api::roles',
    'admin/system/api/users' => 'Magento_Api::users',
    'admin/system/tools/backup' => 'Mage_Backup::backup',
    'admin/system/tools/backup/rollback' => 'Mage_Backup::rollback',
    'admin/catalog/attributes/attributes' => 'Magento_Catalog::attributes_attributes',
    'admin/catalog' => 'Magento_Catalog::catalog',
    'admin/catalog/attributes' => 'Magento_Catalog::catalog_attributes',
    'admin/catalog/categories' => 'Magento_Catalog::categories',
    'admin/system/config/catalog' => 'Magento_Catalog::config_catalog',
    'admin/catalog/products' => 'Magento_Catalog::products',
    'admin/catalog/attributes/sets' => 'Magento_Catalog::sets',
    'admin/catalog/update_attributes' => 'Magento_Catalog::update_attributes',
    'admin/catalog/urlrewrite' => 'Magento_Catalog::urlrewrite',
    'admin/system/config/cataloginventory' => 'Magento_CatalogInventory::cataloginventory',
    'admin/promo' => 'Magento_CatalogRule::promo',
    'admin/promo/catalog' => 'Magento_CatalogRule::promo_catalog',
    'admin/catalog/search' => 'Magento_CatalogSearch::search',
    'admin/system/config/checkout' => 'Magento_Checkout::checkout',
    'admin/sales/checkoutagreement' => 'Magento_Checkout::checkoutagreement',
    'admin/cms/block' => 'Magento_Cms::block',
    'admin/cms' => 'Magento_Cms::cms',
    'admin/system/config/cms' => 'Magento_Cms::config_cms',
    'admin/cms/media_gallery' => 'Magento_Cms::media_gallery',
    'admin/cms/page' => 'Magento_Cms::page',
    'admin/cms/page/delete' => 'Magento_Cms::page_delete',
    'admin/cms/page/save' => 'Magento_Cms::save',
    'admin/system/config/contacts' => 'Magento_Contacts::contacts',
    'admin/system/currency/rates' => 'Magento_CurrencySymbol::currency_rates',
    'admin/system/currency/symbols' => 'Magento_CurrencySymbol::symbols',
    'admin/system/currency' => 'Magento_CurrencySymbol::system_currency',
    'admin/system/config/customer' => 'Magento_Customer::config_customer',
    'admin/customer' => 'Magento_Customer::customer',
    'admin/customer/group' => 'Magento_Customer::group',
    'admin/customer/manage' => 'Magento_Customer::manage',
    'admin/customer/online' => 'Magento_Customer::online',
    'admin/system/design/editor' => 'Mage_DesignEditor::editor',
    'admin/system/config/downloadable' => 'Magento_Downloadable::downloadable',
    'admin/system/config/google' => 'Magento_GoogleCheckout::google',
    'admin/catalog/googleshopping' => 'Magento_GoogleShopping::googleshopping',
    'admin/catalog/googleshopping/items' => 'Magento_GoogleShopping::items',
    'admin/catalog/googleshopping/types' => 'Magento_GoogleShopping::types',
    'admin/system/convert/export' => 'Magento_ImportExport::export',
    'admin/system/convert/import' => 'Magento_ImportExport::import',
    'admin/system/index' => 'Magento_Index::index',
    'admin/newsletter' => 'Magento_Newsletter::admin_newsletter',
    'admin/system/config/newsletter' => 'Magento_Newsletter::newsletter',
    'admin/newsletter/problem' => 'Magento_Newsletter::problem',
    'admin/newsletter/queue' => 'Magento_Newsletter::queue',
    'admin/newsletter/subscriber' => 'Magento_Newsletter::subscriber',
    'admin/newsletter/template' => 'Magento_Newsletter::template',
    'admin/system/api/authorizedTokens' => 'Mage_Oauth::authorizedTokens',
    'admin/system/api/consumer' => 'Mage_Oauth::consumer',
    'admin/system/api/consumer/delete' => 'Mage_Oauth::consumer_delete',
    'admin/system/api/consumer/edit' => 'Mage_Oauth::consumer_edit',
    'admin/system/config/oauth' => 'Mage_Oauth::oauth',
    'admin/system/api/oauth_admin_token' => 'Mage_Oauth::oauth_admin_token',
    'admin/page_cache' => 'Mage_PageCache::page_cache',
    'admin/system/config/payment' => 'Magento_Payment::payment',
    'admin/system/config/payment_services' => 'Magento_Payment::payment_services',
    'admin/report/salesroot/paypal_settlement_reports/fetch' => 'Magento_Paypal::fetch',
    'admin/system/config/paypal' => 'Magento_Paypal::paypal',
    'admin/report/salesroot/paypal_settlement_reports' => 'Magento_Paypal::paypal_settlement_reports',
    'admin/report/salesroot/paypal_settlement_reports/view' => 'Magento_Paypal::paypal_settlement_reports_view',
    'admin/system/config/persistent' => 'Mage_Persistent::persistent',
    'admin/cms/poll' => 'Magento_Poll::poll',
    'admin/catalog/reviews_ratings/ratings' => 'Magento_Rating::ratings',
    'admin/report/shopcart/abandoned' => 'Magento_Reports::abandoned',
    'admin/report/customers/accounts' => 'Magento_Reports::accounts',
    'admin/report/products/bestsellers' => 'Magento_Reports::bestsellers',
    'admin/report/salesroot/coupons' => 'Magento_Reports::coupons',
    'admin/report/customers' => 'Magento_Reports::customers',
    'admin/report/customers/orders' => 'Magento_Reports::customers_orders',
    'admin/report/products/downloads' => 'Magento_Reports::downloads',
    'admin/report/salesroot/invoiced' => 'Magento_Reports::invoiced',
    'admin/report/products/lowstock' => 'Magento_Reports::lowstock',
    'admin/report/tags/popular' => 'Magento_Reports::popular',
    'admin/report/shopcart/product' => 'Magento_Reports::product',
    'admin/report/salesroot/refunded' => 'Magento_Reports::refunded',
    'admin/report' => 'Magento_Reports::report',
    'admin/report/products' => 'Magento_Reports::report_products',
    'admin/report/search' => 'Magento_Reports::report_search',
    'admin/system/config/reports' => 'Magento_Reports::reports',
    'admin/report/review' => 'Magento_Reports::review',
    'admin/report/review/customer' => 'Magento_Reports::review_customer',
    'admin/report/review/product' => 'Magento_Reports::review_product',
    'admin/report/salesroot' => 'Magento_Reports::salesroot',
    'admin/report/salesroot/sales' => 'Magento_Reports::salesroot_sales',
    'admin/report/salesroot/shipping' => 'Magento_Reports::shipping',
    'admin/report/shopcart' => 'Magento_Reports::shopcart',
    'admin/report/products/sold' => 'Magento_Reports::sold',
    'admin/report/statistics' => 'Magento_Reports::statistics',
    'admin/report/tags' => 'Magento_Reports::tags',
    'admin/report/tags/customer' => 'Magento_Reports::tags_customer',
    'admin/report/tags/product' => 'Magento_Reports::tags_product',
    'admin/report/salesroot/tax' => 'Magento_Reports::tax',
    'admin/report/customers/totals' => 'Magento_Reports::totals',
    'admin/report/products/viewed' => 'Magento_Reports::viewed',
    'admin/catalog/reviews_ratings/reviews/pending' => 'Magento_Review::pending',
    'admin/catalog/reviews_ratings/reviews' => 'Magento_Review::reviews',
    'admin/catalog/reviews_ratings/reviews/all' => 'Magento_Review::reviews_all',
    'admin/catalog/reviews_ratings' => 'Magento_Review::reviews_ratings',
    'admin/system/config/rss' => 'Magento_Rss::rss',
    'admin/sales/order/actions' => 'Mage_Sales::actions',
    'admin/sales/order/actions/edit' => 'Mage_Sales::actions_edit',
    'admin/sales/billing_agreement/actions/manage' => 'Mage_Sales::actions_manage',
    'admin/sales/order/actions/view' => 'Mage_Sales::actions_view',
    'admin/sales/billing_agreement' => 'Mage_Sales::billing_agreement',
    'admin/sales/billing_agreement/actions' => 'Mage_Sales::billing_agreement_actions',
    'admin/sales/billing_agreement/actions/view' => 'Mage_Sales::billing_agreement_actions_view',
    'admin/sales/order/actions/cancel' => 'Mage_Sales::cancel',
    'admin/sales/order/actions/capture' => 'Mage_Sales::capture',
    'admin/sales/order/actions/comment' => 'Mage_Sales::comment',
    'admin/system/config/sales' => 'Mage_Sales::config_sales',
    'admin/sales/order/actions/create' => 'Mage_Sales::create',
    'admin/sales/order/actions/creditmemo' => 'Mage_Sales::creditmemo',
    'admin/sales/order/actions/email' => 'Mage_Sales::email',
    'admin/sales/order/actions/emails' => 'Mage_Sales::emails',
    'admin/sales/order/actions/hold' => 'Mage_Sales::hold',
    'admin/sales/order/actions/invoice' => 'Mage_Sales::invoice',
    'admin/system/order_statuses' => 'Mage_Sales::order_statuses',
    'admin/sales/recurring_profile' => 'Mage_Sales::recurring_profile',
    'admin/sales/order/actions/reorder' => 'Mage_Sales::reorder',
    'admin/sales/order/actions/review_payment' => 'Mage_Sales::review_payment',
    'admin/sales' => 'Mage_Sales::sales',
    'admin/sales/creditmemo' => 'Mage_Sales::sales_creditmemo',
    'admin/system/config/sales_email' => 'Mage_Sales::sales_email',
    'admin/sales/invoice' => 'Mage_Sales::sales_invoice',
    'admin/sales/order' => 'Mage_Sales::sales_order',
    'admin/system/config/sales_pdf' => 'Mage_Sales::sales_pdf',
    'admin/sales/order/actions/ship' => 'Mage_Sales::ship',
    'admin/sales/shipment' => 'Mage_Sales::shipment',
    'admin/sales/transactions' => 'Mage_Sales::transactions',
    'admin/sales/transactions/fetch' => 'Mage_Sales::transactions_fetch',
    'admin/sales/order/actions/unhold' => 'Mage_Sales::unhold',
    'admin/sales/billing_agreement/actions/use' => 'Mage_Sales::use',
    'admin/system/config/promo' => 'Mage_SalesRule::config_promo',
    'admin/promo/quote' => 'Mage_SalesRule::quote',
    'admin/system/config/carriers' => 'Mage_Shipping::carriers',
    'admin/system/config/shipping' => 'Mage_Shipping::config_shipping',
    'admin/system/config/sitemap' => 'Magento_Sitemap::config_sitemap',
    'admin/catalog/sitemap' => 'Magento_Sitemap::sitemap',
    'admin/catalog/tag' => 'Magento_Tag::tag',
    'admin/catalog/tag/all' => 'Magento_Tag::tag_all',
    'admin/catalog/tag/pending' => 'Magento_Tag::tag_pending',
    'admin/sales/tax/classes_customer' => 'Mage_Tax::classes_customer',
    'admin/sales/tax/classes_product' => 'Mage_Tax::classes_product',
    'admin/system/config/tax' => 'Mage_Tax::config_tax',
    'admin/sales/tax/import_export' => 'Mage_Tax::import_export',
    'admin/sales/tax/rules' => 'Mage_Tax::rules',
    'admin/sales/tax' => 'Mage_Tax::sales_tax',
    'admin/sales/tax/rates' => 'Mage_Tax::tax_rates',
    'admin/system/acl' => 'Mage_User::acl',
    'admin/system/acl/roles' => 'Mage_User::acl_roles',
    'admin/system/acl/users' => 'Mage_User::acl_users',
    'admin/cms/widget_instance' => 'Mage_Widget::widget_instance',
    'admin/system/config/wishlist' => 'Magento_Wishlist::config_wishlist',
    'admin/xmlconnect/history' => 'Mage_XmlConnect::history',
    'admin/xmlconnect/mobile' => 'Mage_XmlConnect::mobile',
    'admin/xmlconnect/templates' => 'Mage_XmlConnect::templates',
    'admin/xmlconnect' => 'Mage_XmlConnect::xmlconnect',
    'admin/xmlconnect/queue' => 'Mage_XmlConnect::xmlconnect_queue',
    'admin/system/config/facebook' => 'Social_Facebook::facebook',
);

$tableName = $installer->getTable('admin_rule');
/** @var Magento_DB_Adapter_Interface $connection */
$connection = $installer->getConnection();

$select = $connection->select();
$select->from($tableName, array())
    ->columns(array('resource_id' => 'resource_id'))
    ->group('resource_id');

foreach ($connection->fetchCol($select) as $oldKey) {
    /**
     * If used ACL key is converted previously or we haven't map for specified ACL resource item
     * than go to the next item
     */
    if (in_array($oldKey, $map) || false == isset($map[$oldKey])) {
        continue;
    }

    /** Update rule ACL key from xpath format to identifier format */
    $connection->update($tableName, array('resource_id' => $map[$oldKey]), array('resource_id = ?' => $oldKey));
}
$installer->endSetup();

