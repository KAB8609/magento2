<?php
/**
 * Obsolete constants
 *
 * Format: array(<constant_name>[, <class_scope> = ''[, <replacement>]])
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('BACKORDERS_BELOW'),
    array('BACKORDERS_YES'),
    array('CACHE_TAG', '\Magento\Api\Model\Config', '\Magento\Api\Model\Cache\Type::CACHE_TAG'),
    array('CACHE_TAG', '\Magento\Core\Model\AppInterface'),
    array('CACHE_TAG', '\Magento\Core\Model\Resource\Db\Collection\AbstractCollection',
        '\Magento\Core\Model\Cache\Type\Collection::CACHE_TAG'
    ),
    array('CACHE_TAG', '\Magento\Core\Model\Translate', '\Magento\Core\Model\Cache\Type\Translate::CACHE_TAG'),
    array('CACHE_TAG', '\Magento\Rss\Block\Catalog\NotifyStock'),
    array('CACHE_TAG', '\Magento\Rss\Block\Catalog\Review'),
    array('CACHE_TAG', '\Magento\Rss\Block\Order\New'),
    array('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_ONLY'),
    array('CATEGORY_APPLY_CATEGORY_AND_PRODUCT_RECURSIVE'),
    array('CATEGORY_APPLY_CATEGORY_ONLY'),
    array('CATEGORY_APPLY_CATEGORY_RECURSIVE'),
    array('CHECKOUT_METHOD_GUEST'),
    array('CHECKOUT_METHOD_REGISTER'),
    array('CHECKSUM_KEY_NAME'),
    array('CONFIG_TEMPLATE_INSTALL_DATE', '\Magento\Core\Model\Config',
        '\Magento\Core\Model\Config\Primary::CONFIG_TEMPLATE_INSTALL_DATE'
    ),
    array('CONFIG_XML_PATH_DEFAULT_PRODUCT_TAX_GROUP'),
    array('CONFIG_XML_PATH_DISPLAY_FULL_SUMMARY'),
    array('CONFIG_XML_PATH_DISPLAY_TAX_COLUMN'),
    array('CONFIG_XML_PATH_DISPLAY_ZERO_TAX'),
    array('CONFIG_XML_PATH_SHOW_IN_CATALOG'),
    array('DEFAULT_CURRENCY', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::DEFAULT_CURRENCY'),
    array('DEFAULT_ERROR_HANDLER', '\Magento\Core\Model\App', 'Mage::DEFAULT_ERROR_HANDLER'),
    array('DEFAULT_LOCALE', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::DEFAULT_LOCALE'),
    array('DEFAULT_THEME_NAME', 'Magento_Core_Model_Design_PackageInterface'),
    array('DEFAULT_THEME_NAME', 'Magento_Core_Model_Design_Package'),
    array('DEFAULT_TIMEZONE', '\Magento\Core\Model\Locale', 'Mage::DEFAULT_TIMEZONE'),
    array('DEFAULT_VALUE_TABLE_PREFIX'),
    array('ENTITY_PRODUCT', '\Magento\Review\Model\Review'),
    array('EXCEPTION_CODE_IS_GROUPED_PRODUCT'),
    array('FALLBACK_MAP_DIR', 'Magento_Core_Model_Design_PackageInterface'),
    array('FORMAT_TYPE_FULL', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_FULL'),
    array('FORMAT_TYPE_LONG', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_LONG'),
    array('FORMAT_TYPE_MEDIUM', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM'),
    array('FORMAT_TYPE_SHORT', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT'),
    array('GALLERY_IMAGE_TABLE', '\Magento\Catalog\Model\Resource\Product\Attribute\Backend\Media'),
    array('HASH_ALGO'),
    array('INIT_OPTION_DIRS', '\Magento\Core\Model\App', 'Mage::PARAM_APP_DIRS'),
    array('INIT_OPTION_REQUEST', 'Mage'),
    array('INIT_OPTION_REQUEST', '\Magento\Core\Model\App'),
    array('INIT_OPTION_RESPONSE', 'Mage'),
    array('INIT_OPTION_RESPONSE', '\Magento\Core\Model\App'),
    array('INIT_OPTION_SCOPE_CODE', '\Magento\Core\Model\App', 'Mage::PARAM_RUN_CODE'),
    array('INIT_OPTION_SCOPE_TYPE', '\Magento\Core\Model\App', 'Mage::PARAM_RUN_TYPE'),
    array('INIT_OPTION_URIS', '\Magento\Core\Model\App', 'Mage::PARAM_APP_URIS'),
    array('INSTALLER_HOST_RESPONSE', '\Magento\Install\Model\Installer'),
    array('LAYOUT_GENERAL_CACHE_TAG', '\Magento\Core\Model\Layout\Merge', '\Magento\Core\Model\Cache\Type\Layout::CACHE_TAG'),
    array('LOCALE_CACHE_KEY', '\Magento\Adminhtml\Block\Page\Footer'),
    array('LOCALE_CACHE_LIFETIME', '\Magento\Adminhtml\Block\Page\Footer'),
    array('LOCALE_CACHE_TAG', '\Magento\Adminhtml\Block\Page\Footer'),
    array('PATH_PREFIX_CUSTOMIZATION', '\Magento\Core\Model\Theme'),
    array('PATH_PREFIX_CUSTOMIZED', '\Magento\Core\Model\Theme\Files'),
    array('PUBLIC_BASE_THEME_DIR', 'Magento_Core_Model_Design_PackageInterface'),
    array('PUBLIC_CACHE_TAG', 'Magento_Core_Model_Design_PackageInterface'),
    array('PUBLIC_MODULE_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_MODULE_DIR'
    ),
    array('PUBLIC_THEME_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_THEME_DIR'
    ),
    array('PUBLIC_VIEW_DIR', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::PUBLIC_VIEW_DIR'
    ),
    array('REGISTRY_FORM_PARAMS_KEY', null, 'direct value'),
    array('SCOPE_TYPE_GROUP', '\Magento\Core\Model\App', '\Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_GROUP'),
    array('SCOPE_TYPE_STORE', '\Magento\Core\Model\App', '\Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE'),
    array('SCOPE_TYPE_WEBSITE', '\Magento\Core\Model\App', '\Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_WEBSITE'),
    array('SEESION_MAX_COOKIE_LIFETIME'),
    array('TYPE_BINARY', null, '\Magento\DB\Ddl\Table::TYPE_BLOB'),
    array('TYPE_CHAR', null, '\Magento\DB\Ddl\Table::TYPE_TEXT'),
    array('TYPE_CLOB', null, '\Magento\DB\Ddl\Table::TYPE_TEXT'),
    array('TYPE_DOUBLE', null, '\Magento\DB\Ddl\Table::TYPE_FLOAT'),
    array('TYPE_LONGVARBINARY', null, '\Magento\DB\Ddl\Table::TYPE_BLOB'),
    array('TYPE_LONGVARCHAR', null, '\Magento\DB\Ddl\Table::TYPE_TEXT'),
    array('TYPE_REAL', null, '\Magento\DB\Ddl\Table::TYPE_FLOAT'),
    array('TYPE_TIME', null, '\Magento\DB\Ddl\Table::TYPE_TIMESTAMP'),
    array('TYPE_TINYINT', null, '\Magento\DB\Ddl\Table::TYPE_SMALLINT'),
    array('TYPE_VARCHAR', null, '\Magento\DB\Ddl\Table::TYPE_TEXT'),
    array('URL_TYPE_SKIN'),
    array('XML_PATH_ALLOW_CODES', '\Magento\Core\Model\Locale', '\Magento\Core\Model\LocaleInterface::XML_PATH_ALLOW_CODES'),
    array('XML_PATH_ALLOW_CURRENCIES', '\Magento\Core\Model\Locale',
        '\Magento\Core\Model\LocaleInterface::XML_PATH_ALLOW_CURRENCIES'
    ),
    array('XML_PATH_ALLOW_CURRENCIES_INSTALLED', '\Magento\Core\Model\Locale',
        '\Magento\Core\Model\LocaleInterface::XML_PATH_ALLOW_CURRENCIES_INSTALLED'),
    array('XML_PATH_ALLOW_DUPLICATION', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_ALLOW_DUPLICATION'
    ),
    array('XML_PATH_ALLOW_MAP_UPDATE', 'Mage_Core_Model_Design_PackageInterface'),
    array('XML_PATH_BACKEND_FRONTNAME', 'Mage_Backend_Helper_Data'),
    array('XML_PATH_CACHE_BETA_TYPES'),
    array('XML_PATH_CHECK_EXTENSIONS', '\Magento\Install\Model\Config'),
    array('XML_PATH_COUNTRY_DEFAULT', '\Magento\Paypal\Model\System\Config\Backend\MerchantCountry'),
    array('XML_PATH_DEFAULT_COUNTRY', '\Magento\Core\Model\Locale'),
    array('XML_PATH_DEFAULT_LOCALE', '\Magento\Core\Model\Locale',
        '\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_LOCALE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', '\Magento\Core\Model\Locale',
        '\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE'
    ),
    array('XML_PATH_INSTALL_DATE', 'Mage_Core_Model_App', 'Mage_Core_Model_Config_Primary::XML_PATH_INSTALL_DATE'),
    array('XML_PATH_LOCALE_INHERITANCE', 'Mage_Core_Model_Translate'),
    array('XML_PATH_SENDING_SET_RETURN_PATH', 'Mage_Newsletter_Model_Subscriber'),
    array('XML_PATH_SKIP_PROCESS_MODULES_UPDATES', 'Mage_Core_Model_App',
        'Mage_Core_Model_Db_UpdaterInterface::XML_PATH_SKIP_PROCESS_MODULES_UPDATES'
    ),
    array('XML_PATH_STATIC_FILE_SIGNATURE', '\Magento\Core\Helper\Data',
        'Magento_Core_Model_Design_Package::XML_PATH_STATIC_FILE_SIGNATURE'
    ),
    array('XML_PATH_THEME', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_THEME'
    ),
    array('XML_PATH_THEME_ID', 'Magento_Core_Model_Design_PackageInterface',
        'Magento_Core_Model_Design_Package::XML_PATH_THEME_ID'
    ),
    array('XML_STORE_ROUTERS_PATH', 'Mage_Core_Controller_Varien_Front'),
);

