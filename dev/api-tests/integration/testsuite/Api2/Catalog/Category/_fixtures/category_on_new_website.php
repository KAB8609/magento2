<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_Webservice::getFixture('category_on_new_website')) {
    $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixture');
    /* @var $rootCategory Mage_Catalog_Model_Category */
    $rootCategory = require $fixturesDir . '/_block/Catalog/Category.php';
    $rootCategory->save();

    // create new store fuxture
    require realpath(dirname(__FILE__) . '/../../_fixtures/store_on_new_website.php');
    /** @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = Magento_Test_Webservice::getFixture('store_group');
    $storeGroup->setRootCategoryId($rootCategory->getId())->save();

    Magento_Test_Webservice::setFixture('category_on_new_website', $rootCategory,
        Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
}
