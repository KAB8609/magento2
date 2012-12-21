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
if (!Magento_Test_Webservice::getFixture('store')) {

    $category = Mage::getModel('Mage_Catalog_Model_Category');
    $category->setData(array(
        'name' => 'Category Test Created ' . uniqid(),
        'is_active' => 1,
        'is_anchor' => 1,
        'landing_page' => 1, //ID of CMS block
        'position' => 100,
        'description' => 'some description',
        'default_sort_by' => 'name',
        'available_sort_by' => array('name'),
        'display_mode' => Mage_Catalog_Model_Category::DM_PRODUCT,
        'landing_page' => 1, //ID of static block
        'include_in_menu' => 1,
        'page_layout' => 'one_column',
        'custom_design' => 'default/default/default',
        'custom_design_apply' => 'someValue', //deprecated attribute, should be empty
        'custom_design_from' => date('Y-m-d'), //date of start use design
        'custom_design_to' => date('Y-m-d', time() + 24*3600), //date of finish use design
        'custom_layout_update' => '<block type="core/text_list" name="content" output="toHtml"/>',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'meta_title' => 'Meta title',
        'url_key' => 'url-key' . uniqid()
    ));
    $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
    $parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($parentId);
    $category->setPath($parentCategory->getPath());
    $category->setStoreId(0);
    $category->save();
    Magento_Test_Webservice::setFixture('category', $category, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);


    $website = Mage::getModel('Mage_Core_Model_Website');
    $website->setData(
        array(
            'code' => 'test_' . uniqid(),
            'name' => 'test website' . uniqid()
        )
    );
    $website->save();
    Magento_Test_Webservice::setFixture('website', $website, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);

    $storeGroup = Mage::getModel('Mage_Core_Model_Store_Group');
    $storeGroup->setData(array(
        'website_id' => $website->getId(),
        'name' => 'Test Store' . uniqid(),
        'code' => 'store_group_' . uniqid(),
        'root_category_id' => $category->getId()
    ))->save();
    Magento_Test_Webservice::setFixture('store_group', $storeGroup, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);


    $store = Mage::getModel('Mage_Core_Model_Store');
    $store->setData(array(
        'group_id' => $storeGroup->getId(),
        'name' => 'Test Store View' . uniqid(),
        'code' => 'store_' . uniqid(),
        'is_active' => true,
        'website_id' => $website->getId()
    ))->save();
    Mage::app()->reinitStores();
    Magento_Test_Webservice::setFixture('store', $store, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
}
