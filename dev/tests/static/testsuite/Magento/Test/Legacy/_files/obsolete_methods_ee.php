<?php
/**
 * Same as obsolete_methods.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('_filterIndexData', '\Magento\Search\Model\Adapter\AbstractAdapter'),
    array('getSearchTextFields', '\Magento\Search\Model\Adapter\AbstractAdapter'),
    array('addAppliedRuleFilter', '\Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addBannersFilter', '\Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addBannersFilter', '\Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('addCategoryFilter', '\Magento\Search\Model\Catalog\Layer\Filter\Category'),
    array('addCustomerSegmentFilter', '\Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('addCustomerSegmentFilter', '\Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('addFieldsToBannerForm', '\Magento\CustomerSegment\Model\Observer'),
    array('setModelName', '\Magento\Logging\Model\Event\Changes'),
    array('getModelName', '\Magento\Logging\Model\Event\Changes'),
    array('setModelId', '\Magento\Logging\Model\Event\Changes'),
    array('getModelId', '\Magento\Logging\Model\Event\Changes'),
    array('_initAction', '\Magento\AdvancedCheckout\Controller\Adminhtml\Checkout'),
    array('getEventData', '\Magento\Logging\Block\Adminhtml\Container'),
    array('getEventXForwardedIp', '\Magento\Logging\Block\Adminhtml\Container'),
    array('getEventIp', '\Magento\Logging\Block\Adminhtml\Container'),
    array('getEventError', '\Magento\Logging\Block\Adminhtml\Container'),
    array('postDispatchSystemStoreSave', '\Magento\Logging\Model\Handler\Controllers'),
    array('getUrls', '\Magento\FullPageCache\Model\Crawler'),
    array('getUrlStmt', '\Magento\FullPageCache\Model\Resource\Crawler'),
    array('_getLinkCollection', '\Magento\TargetRule\Block\Checkout\Cart\Crosssell'),
    array('getCustomerSegments', '\Magento\CustomerSegment\Model\Resource\Customer'),
    array('getRequestUri', '\Magento\FullPageCache\Model\Processor\DefaultProcessor'),
    array('_getActiveEntity', '\Magento\GiftRegistry\Controller\Index'),
    array('getActiveEntity', '\Magento\GiftRegistry\Model\Entity'),
    array('_convertDateTime', '\Magento\CatalogEvent\Model\Event'),
    array('updateStatus', '\Magento\CatalogEvent\Model\Event'),
    array('getStateText', '\Magento\GiftCardAccount\Model\Giftcardaccount'),
    array('getStoreContent', '\Magento\Banner\Model\Banner'),
    array('_searchSuggestions', '\Magento\Search\Model\Adapter\HttpStream'),
    array('_searchSuggestions', '\Magento\Search\Model\Adapter\PhpExtension'),
    array('updateCategoryIndexData', '\Magento\Search\Model\Resource\Index'),
    array('updatePriceIndexData', '\Magento\Search\Model\Resource\Index'),
    array('_changeIndexesStatus', '\Magento\Search\Model\Indexer\Indexer'),
    array('cmsPageBlockLoadAfter', '\Magento\AdminGws\Model\Models'),
    array('applyEventStatus', '\Magento\CatalogEvent\Model\Observer'),
    array('checkQuoteItem', '\Magento\CatalogPermissions\Model\Observer'),
    array('increaseOrderInvoicedAmount', '\Magento\GiftCardAccount\Model\Observer'),
    array('blockCreateAfter', '\Magento\FullPageCache\Model\Observer'),
    array('_checkViewedProducts', '\Magento\FullPageCache\Model\Observer'),
    array('invoiceSaveAfter', '\Magento\Reward\Model\Observer'),
    array('_calcMinMax', '\Magento\GiftCard\Block\Catalog\Product\Price'),
    array('_getAmounts', '\Magento\GiftCard\Block\Catalog\Product\Price'),
    array('searchSuggestions', '\Magento\Search\Model\Client\Solr'),
    array('_registerProductsView', '\Magento\FullPageCache\Model\Container\Viewedproducts'),
    array('_getForeignKeyName', 'Magento_DB_Adapter_Oracle'),
    array('getCacheInstance', '\Magento\FullPageCache\Model\Cache'),
    array('saveCustomerSegments', '\Magento\Banner\Model\Resource\Banner'),
    array('saveOptions', '\Magento\FullPageCache\Model\Cache'),
    array('refreshRequestIds', '\Magento\FullPageCache\Model\Processor',
        '\Magento\FullPageCache\Model\Request\Identifier::refreshRequestIds'
    ),
    array('resetColumns', '\Magento\Banner\Model\Resource\Salesrule\Collection'),
    array('resetSelect', '\Magento\Banner\Model\Resource\Catalogrule\Collection'),
    array('prepareCacheId', '\Magento\FullPageCache\Model\Processor',
        '\Magento\FullPageCache\Model\Request\Identifier::prepareCacheId'
    ),
    array('_getQuote', '\Magento\AdvancedCheckout\Block\Adminhtml\Manage\Form\Coupon',
        '\Magento\AdvancedCheckout\Block\Adminhtml\Manage\Form\Coupon::getQuote()'
    ),
    array('_getQuote', '\Magento\GiftCardAccount\Block\Checkout\Cart\Total',
        '\Magento\GiftCardAccount\Block\Checkout\Cart\Total::getQuote()'
    ),
    array('_getQuote', '\Magento\GiftCardAccount\Block\Checkout\Onepage\Payment\Additional',
        '\Magento\GiftCardAccount\Block\Checkout\Onepage\Payment\Additional::getQuote()'
    ),
    array('_getQuote', '\Magento\GiftWrapping\Block\Checkout\Options',
        '\Magento\GiftWrapping\Block\Checkout\Options::getQuote()'
    ),
    array('addCustomerSegmentRelationsToCollection', '\Magento\TargetRule\Model\Resource\Rule'),
    array('_getRuleProductsTable', '\Magento\TargetRule\Model\Resource\Rule'),
    array('getCustomerSegmentRelations', '\Magento\TargetRule\Model\Resource\Rule'),
    array('_saveCustomerSegmentRelations', '\Magento\TargetRule\Model\Resource\Rule'),
    array('_prepareRuleProducts', '\Magento\TargetRule\Model\Resource\Rule'),
    array('getInetNtoaExpr', 'Magento_Logging_Model_Resource_Helper_Mysql4'),
);
