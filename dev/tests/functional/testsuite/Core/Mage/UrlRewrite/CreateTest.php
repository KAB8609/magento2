<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_UrlRewrite
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url Rewrite Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_UrlRewrite_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->closeLastWindow();
    }

    protected function tearDownAfterTestClass()
    {
        $this->frontend();
        $this->fillDropdown('select_store', 'Main Website Store');
        $this->validatePage();
    }

    /**
     * <p>Verify that url rewrite form is present at the backend page<p>
     * <p>Steps to reproduce:<p>
     * <p>1. Navigate to the form to add new custom url rewrite<p>
     * <p>Expected result:</p>
     * <p>A form to enter url rewrite is present</p>
     * <p>Only one instance of such form is present</p>
     * <p>The form has proper attributes (matched by uimap xpath)<p>
     *
     * @test
     */
    public function testFormIsPresent()
    {
        //Steps
        $this->navigate('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'create_url_rewrite_dropdown'),
            'Create URL Rewrite dropdown is not present');
        $this->assertTrue($this->controlIsPresent('button', 'back'), 'Back button is not present');
        $this->assertTrue($this->controlIsPresent('fieldset', 'category_rewrite'),
            'Select Category fieldset is not present');
    }

    /**
     * <p>Verifying Required field for Custom URL rewrite</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. At Create URL rewrite dropdown select Custom</p>
     * <p>4. Click Save button</p>
     * <p>Expected result:</p>
     * <p>Custom URL rewrite does not created</p>
     * <p>Message "This is a required field." is displayed</p>
     *
     * @param string $emptyField
     * @param string $messageCount
     *
     * @test
     * @author Michael Banin
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5518
     */
    public function withRequiredFieldsEmpty($emptyField, $messageCount)
    {
        //Loading data from data file
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom', array($emptyField => '%noValue%'));
        //Open URL rewrite management page
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select Custom
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Fill required fields except one
        $this->fillForm($fieldData);
        //Click Save button
        $this->saveForm('save');
        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('id_path', 1),
            array ('request_path', 1),
            array ('target_path', 1)
        );
    }

    /**
     * <p>Verifying Required field for Product URl rewrite</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For product"</p>
     * <p>4. Select Product in Grid</p>
     * <p>5. Select Category</p>
     * <p>Expected result:</p>
     * <p>"ID Path" & "Target Path" won't editable</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5517
     */
    public function withRequiredFieldsNotEditable()
    {
        //Create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);

        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');

        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        $this->waitForAjax();

        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();

        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();

        //Select Category
        $categorySearch = $productData['categories'];
        $this->addParameter('rootName', $categorySearch);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();

        //Check fields id_path & target path isn't editable
        if ($this->byId('id_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('ID Path field is editable!');
        }
        if ($this->byId('target_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('Target Path field is editable!');
        }
    }

    /**
     * <p>Verifying Required field for Product URl rewrite</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For Category"</p>
     * <p>4. Select Category</p>
     * <p>Expected result:</p>
     * <p>"Type" & "ID Path" & "Target Path" won't editable</p>
     *
     * @depends withRequiredFieldsNotEditable
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5677
     */
    public function withRequiredFieldsNotEditableForCategory()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');

        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');

        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        $this->waitForAjax();

        //Select "For category"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');

        //Select Category
        $this->validatePage('add_new_urlrewrite_category');
        $categorySearch = $productData['categories'];
        $this->addParameter('rootName', $categorySearch);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('category'));
        $this->validatePage('edit_urlrewrite_category');

        //Check fields id_path & target path isn't editable
        if ($this->byId('id_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('ID Path field is editable!');
        }
        if ($this->byId('target_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('Target Path field is editable!');
        }
    }

    /**
     * <p>Verifying Required field for Custom URl rewrite</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. At Create URL rewrite dropdown select Custom</p>
     * <p>Expected result:</p>
     * <p>"Type" & "ID Path" & "Target Path" won't editable</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5694
     */
    public function withRequiredFieldsNotEditableForCustom()
    {
        //Open Custom page
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();

        // Check fields "id_path" & "target path" is editable
        if (!$this->byId('id_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('ID Path field is not editable!');
        }
        if (!$this->byId('target_path')->enabled()) {
            throw new PHPUnit_Framework_Exception('Target Path field is not editable!');
        }
    }

    /**
     * <p>Create URL rewrite for product</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For product"</p>
     * <p>4. Select Product in Grid</p>
     * <p>5. Select Category (this step can be skipped)</p>
     * <p>6. Input needed Request path</p>
     * <p>7. Save</p>
     * <p>8. Open store on front with new request path</p>
     * <p>Expected result:</p>
     * <p>URL rewrite created and works</p>
     *
     * @return array
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-5503
     */
    public function urlRewriteForProduct()
    {
        //Loading data from data file
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product');
        //Create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        $this->waitForAjax();
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $categorySearch = $productData['categories'];
        $this->addParameter('rootName', $categorySearch);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Fill request path input field
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button
        $this->saveForm('save');
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        $this->waitForPageToLoad();
        //Verifying page of URL rewrite for product
        $this->setCurrentPage('product_page');
        $this->addParameter('productName', $productData['general_name']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'), 'Product not opened');
        $openedProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
        $this->assertEquals($productData['general_name'], $openedProductName,
            "Product with name '$openedProductName' is opened, but should be '{$productData['general_name']}'");
        return $fieldData;
    }

    /**
     * <p>Create URL rewrite for product with existing Request path</p>
     * <p>Steps</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For product"</p>
     * <p>4. Select Product in Grid</p>
     * <p>5. Select Category</p>
     * <p>6. Input existing Request path</p>
     * <p>7. Save</p>
     * <p>8. Select</p>
     * <p>Expected result: message that this Request path is already exist appear</p>
     *
     * @depends urlRewriteForProduct
     *
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-5514
     */
    public function urlRewriteForProductExistingReqPath($fieldData)
    {
        //Create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        $this->waitForAjax();
        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();
        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();
        //Select Category
        $categorySearch = $productData['categories'];
        $this->addParameter('rootName', $categorySearch);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();
        //Fill request path input field with existing data
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button
        $this->clickButton('save', false);
        $this->waitForPageToLoad();
        //Check that validation message appear
        $this->assertMessagePresent('validation', 'req_path_exist');
    }

    /**
     * <p>CMS Pages custom URL Rewrites</p>
     * <p>Steps</p>
     * <p>1. Create CMS Page </p>
     * <p>2. Push "Add URL Rewrite" Button</p>
     * <p>3. Select Custom URL rewrite</p>
     * <p>4. Request path = [url_key] ; ID_Path = [url_key]</p>
     * <p>5. Target path = http://magentocommerce.com
     * <p>Expected result:</p>
     * <p>When I'll open page with [url-key] should open http://magentocommerce.com CMS page</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-6123
     */
    public function cmsPageRewriteExtLink()
    {
        $this->markTestSkipped('Skipped due to bug MAGETWO-3263');
        //Create data
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //$productData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom_sample');
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');

        //Create CMS Page
        $this->navigate('manage_cms_pages');

        $this->clickButton('add_new_page');
        $this->fillFieldset($pageData['page_information'], 'page_information_fieldset');

        $this->openTab('content');
        $this->fillField('content_heading', 'test');
        $this->clickButton('show_hide_editor', false);
        $this->waitForAjax();
        $this->fillField('editor', 'test');
        $this->validatePage();
        $this->waitForAjax();
        $this->clickButton('save_page');
        $this->assertMessagePresent('success', 'success_saved_cms_page');

        //Create Custom URL rewrite
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();

        //Fill form and save sitemap
        $this->fillField('target_path', 'http://magentocommerce.com');
        $this->fillField('id_path', $pageData['page_information']['url_key']);
        $this->fillField('request_path', $pageData['page_information']['url_key']);
        $this->saveForm('save');

        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');

        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['page_information']['url_key']);

        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);

        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Magento - Home - eCommerce Software for Growth', 'Wrong page is opened');
    }

    /**
     * <p>CMS Pages custom URL Rewrites</p>
     * <p>Steps</p>
     * <p>1. Create CMS Page </p>
     * <p>2. Push "Add URL Rewrite" Button</p>
     * <p>3. Select Custom URL rewrite</p>
     * <p>4. Request path = [url_key] ; ID_Path = [url_key]</p>
     * <p>5. Target path = customer-service
     * <p>Expected result:</p>
     * <p>When I'll open page with [url-key] should open customer-service CMS page</p>
     *
     * @return array
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-6049
     */
    public function withRequiredFieldsCmsPageRewrite()
    {
        //Create data
        $productData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom_sample');
        $pageData = $this->loadDataSet('UrlRewrite', 'url_cms_page_req');

        //Create CMS Page
        $this->navigate('manage_cms_pages');

        $this->clickButton('add_new_page');
        $this->fillFieldset($pageData['page_information'], 'page_information_fieldset');

        $this->openTab('content');
        $this->fillField('content_heading', 'test');
        $this->clickButton('show_hide_editor', false);
        $this->waitForAjax();
        $this->fillField('editor', 'test');
        $this->validatePage();
        $this->waitForAjax();
        $this->clickButton('save_page');
        $this->assertMessagePresent('success', 'success_saved_cms_page');

        //Create Custom URL rewrite
        $this->admin('url_rewrite_management');
        $this->clickButton('add_new_rewrite');
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();

        //Fill form and save sitemap
        $this->fillFieldset($productData, 'custom_rewrite');
        $this->fillField('id_path', $pageData['page_information']['url_key']);
        $this->fillField('request_path', $pageData['page_information']['url_key']);
        $this->saveForm('save');

        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');

        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['page_information']['url_key']);

        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        $this->waitForPageToLoad();

        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Customer Service', 'Wrong page is opened');

        return $pageData;
    }

    /**
     * <p>URL Rewrites using an external link</p>
     * <p>Steps</p>
     * <p>1. Push "Add URL Rewrite" Button</p>
     * <p>2. Select Custom URL rewrite</p>
     * <p>3. Request path = [url_key] ; ID_Path = [url_key]</p>
     * <p>4. Target path = http://google.com
     * <p>Expected result:</p>
     * <p>When I'll try to open request path should open Google page</p>
     *
     * @param array $data
     *
     * @test
     * @author denis.poloka
     * @depends withRequiredFieldsCmsPageRewrite
     * @TestlinkId TL-MAGE-5507
     */
    public function withRequiredFieldsRewriteExtLink($data)
    {
        //Create data
        //$productData = $this->loadDataSet('UrlRewrite', 'url_rewrite_custom_sample');
        $pageData = $this->loadDataSet('UrlRewrite', 'url_search_url',
            array('url_key' => $data['page_information']['url_key']));

        $this->navigate('url_rewrite_management');
        $this->validatePage();
        $this->searchAndOpen($pageData, 'url_rewrite_grid');
        $this->addParameter('id', $this->defineParameterFromUrl('customId'));

        //Fill form and save sitemap
        $this->fillField('target_path', 'http://google.com');
        $this->saveForm('save');

        //Verifying
        $this->assertMessagePresent('success', 'success_saved_url_rewrite');

        //Flush Magento cache
        $this->flushCache();

        //Generate request path
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($pageData['url_key']);

        //Open page on frontend
        $this->frontend();
        $this->url($rewriteUrl);

        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), 'Google', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrites using an external link</p>
     * <p>Steps</p>
     * <p>1. Press Add URL Rewrite button</p>
     * <p>2. Create URL Rewrite select Product</p>
     * <p>3. Click on your product</p>
     * <p>4. Select Request store, Select Target Store (the same as Request Store) and click Save button</p>
     * <p>5. Open second store and add to the address line the Request Path from step 4</p>
     * <p>Expected result:</p>
     * <p>404 page is opened</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5508
     */
    public function productRewriteOfOneStore()
    {
        //Create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        //        $productSearch =
        //            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);

        //Create Store and Store View
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->navigate('manage_stores');

        //Create Store
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');

        //Create StoreView
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        //Generate request path and open it
        $urlKeyReplace = str_replace(array('(', ')'), array('-', ''), $productData['general_url_key']);
        $uri = $urlKeyReplace . '.html';

        $this->addParameter('url_key', $uri);
        $this->addParameter('page_title', $productData['general_name']);
        $this->frontend('test_page');
        $this->assertSame($this->title(), $productData['general_name'], 'Wrong page is opened');

        //Select other store
        $this->frontend();

        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->fillDropdown('select_store', $storeData['store_name']);
        $this->waitForPageToLoad();

        $this->frontend('test_page', false);

        //Verifying page of URL rewrite for product
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrite for category in scope of the same one store</p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For category"</p>
     * <p>4. Select Category in tree</p>
     * <p>5. Input needed Request path</p>
     * <p>6. Save</p>
     * <p>7. Open store on front with new request path</p>
     * <p>Expected result:</p>
     * <p>URL rewrite created and category page is opened</p>
     *
     * @return array
     * @author Michael Banin
     * @test
     * @TestlinkId TL-MAGE-5515
     */
    public function urlRewriteCategory()
    {
        //Created category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //Open URL rewrite management
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select For category
        $this->fillDropdown('create_url_rewrite_dropdown', 'For category');
        //Select Subcategory by name and detect it's id from url
        $this->addParameter('subName', $categoryData['name']);
        $this->clickControl('link', 'sub_category', false);
        $this->waitForPageToLoad();
        $id = $this->defineParameterFromUrl('category', null);
        $this->addParameter('id', $id);
        $currentPage = $this->_findCurrentPageFromUrl();
        $this->setCurrentPage($currentPage);
        //Loading data from data file
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_category');
        //Fill request path input field
        $this->fillField('request_path', $fieldData['request_path']);
        //Click Save button
        $this->clickButton('save', false);
        $this->waitForPageToLoad();
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Open default store on frontend if opened other one
        $this->frontend();
        $this->fillDropdown('select_store', 'Main Website Store');
        $this->waitForPageToLoad();
        //Open URL rewrite for category on frontend
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), $categoryData['name'], 'Wrong page is opened');
        return $fieldData;
    }

    /**
     * <p>URL Rewrite for category in scope of the same one store (URL not available from other store)<p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "For category"</p>
     * <p>4. Select Category in tree</p>
     * <p>5. Input needed Request path</p>
     * <p>6. Save</p>
     * <p>7. Open other store on front with new request path</p>
     * <p>Expected result:</p>
     * <p>404 page is opened</p>
     *
     * @param $fieldData
     *
     * @test
     * @author Michael Banin
     * @depends urlRewriteCategory
     * @TestlinkId TL-MAGE-5516
     */
    public function categoryUrlRewriteOtherStore($fieldData)
    {
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);
        //Create Store and Store View
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->navigate('manage_stores');
        //Create Store
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        //Create StoreView
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->fillDropdown('select_store', $storeData['store_name']);
        $this->waitForPageToLoad();
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($fieldData['request_path']);
        //Opening URL rewrite on selected store
        $this->url($rewriteUrl);
        $this->waitForPageToLoad();
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrite for product in scope of two different Websites</p>
     * <p>Steps</p>
     * <p>1. Create Product </p>
     * <p>2. Create Website 1 and Website 2</p>
     * <p>3. Assign Product to Website 2</p>
     * <p>4. Open frontend and try open created product</p>
     * <p>Expected result:</p>
     * <p>Will be opened 404 page</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5510
     */
    public function productRewriteToOtherWebsite()
    {
        //Create Root for Website 2
        $this->navigate('manage_categories');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);
        $this->navigate('manage_stores');

        // Crete Website 2 with Store and Store View
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');

        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');

        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        //Create product and assign to Website2
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite',
            array('websites' => $websiteDataOne['website_name'], 'categories' => $category['name']));
        //        $productSearch =
        //            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        //Generate request path and open it
        $urlKeyReplace = str_replace(array('(', ')'), array('-', ''), $productData['general_url_key']);
        $uri = $urlKeyReplace . '.html';

        //Open product URl
        $this->frontend();
        $this->url($uri);
        $this->waitForPageToLoad();
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p> Category URl rewrite for two different websites</p>
     * <p>1. Create two websites</p>
     * <p>2. Create Category and assigned to each website</p>
     * <p>3. Press "Add URl rewrite button"</p>
     * <p>4. Select Category</p>
     * <p>5. Select Request Store and Target Store</p>
     * <p>6. Save URL Rewrite</p>
     * <p>Expected result:</p>
     * <p>Request path should work correctly</p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5533
     */

    public function forCategoryLinkToOtherWebsite()
    {
        //Create Root for Website 2
        $this->navigate('manage_categories');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);

        //Create SubCategory
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $categoryData = $this->loadDataSet('Category', 'sub_category_required_url_rewrite',
            array('parent_category' => $category['name'], 'url_key' => 'testCategory'));
        $this->categoryHelper()->createCategory($categoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();
        //        $categoryId = $categoryData['parent_category'] . '/' . $categoryData['name'];
        $this->navigate('manage_stores');

        // Crete Website 2 with Store and Store View
        $websiteDataOne = $this->loadDataSet('Website', 'generic_website');
        $this->storeHelper()->createStore($websiteDataOne, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');

        $storeData = $this->loadDataSet('Store', 'generic_store',
            array('website' => $websiteDataOne['website_name'], 'root_category' => $category['name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');

        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        //Generate request path and open it
        $urlKeyReplace = str_replace(array('(', ')'), array('-', ''), $categoryData['url_key']);
        $uri = $urlKeyReplace . '.html';

        //Open product URl
        $this->frontend();
        $this->url($uri);
        $this->waitForPageToLoad();
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>Custom URL Rewrite for product in scope of the same one store<p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "Custom"</p>
     * <p>4. Input needed fields</p>
     * <p>5. Set the same Request store and Target store</p>
     * <p>6. Save</p>
     * <p>7. Open front with new request path</p>
     * <p>Expected result:</p>
     * <p>Product page is opened</p>
     *
     * @return string
     * @test
     * @author Michael Banin
     * @TestlinkId TL-MAGE-5565
     */

    public function customProductUrlRewriteSameStore()
    {
        //Create Simple Product
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_url_rewrite');
        //        $productSearch =
        //            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Open URL rewrite management
        $this->navigate('url_rewrite_management');
        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        //At Create URL rewrite dropdown select For category
        $this->fillDropdown('create_url_rewrite_dropdown', 'Custom');
        $this->waitForPageToLoad();
        $this->validatePage();
        //Loading data from data file
        $fieldData = $this->loadDataSet('UrlRewrite', 'url_rewrite_product_custom');
        //Generate request path and open it
        $urlKeyReplace = str_replace(array('(', ')'), array('-', ''), $productData['general_url_key']);
        $uri = $urlKeyReplace . '.html';
        //Fill fields
        $this->fillField('id_path', $fieldData['id_path']);
        $this->fillField('request_path', $fieldData['request_path']);
        $this->fillField('target_path', $uri);
        //        Need to be uncommented when target store functionality will be merged
        //        $this->fillDropdown('target_store', 'Default Store View');
        $this->fillDropdown('request_store', 'Default Store View');
        //Click Save button
        $this->clickButton('save', false);
        $this->waitForPageToLoad();
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        //Open URL rewrite for category on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), $productData['general_name'], 'Wrong page is opened');
        return ($uri);
    }

    /**
     * <p>Custom product URL rewrite created for the same one store should not work for other store<p>
     * <p>1. Go to URL rewrite management</p>
     * <p>2. Click Add URL rewrite button</p>
     * <p>3. Create URL Rewrite = "Custom"</p>
     * <p>4. Input needed fields</p>
     * <p>5. Set the same Request store and Target store</p>
     * <p>6. Save</p>
     * <p>7. Open other store on the front and URL rewrite on it</p>
     * <p>Expected result:</p>
     * <p>404 page is opened</p>
     *
     * @param string $uri
     *
     * @test
     * @author Michael Banin
     * @depends customProductUrlRewriteSameStore
     * @TestlinkId TL-MAGE-5571
     */

    public function customProductUrlRewriteOtherStore($uri)
    {
        //Create Category
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);
        //Create Store and Store View
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->navigate('manage_stores');
        //Create Store
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        //Create StoreView
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Select other store
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $storeViewData['store_view_code']);
        $this->fillDropdown('select_store', $storeData['store_name']);
        $this->waitForPageToLoad();
        //Generating URL rewrite link
        $rewriteUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        //Open URL rewrite for category on frontend
        $this->frontend();
        $this->url($rewriteUrl);
        $this->waitForPageToLoad();
        //Verifying page of URL rewrite for category
        $this->assertSame($this->title(), '404 Not Found 1', 'Wrong page is opened');
    }

    /**
     * <p>URL Rewrite for product in scope of two different Websites</p>
     * <p>Steps</p>
     * <p>1. Press Add URL Rewrite button</p>
     * <p>2. Create URL Rewrite select Product</p>
     * <p>3. Select category </p>
     * <p>4. Request store dropdown Store 2 does not available </p>
     *
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-5512
     */
    public function productRewriteToOtherStore()
    {
        //Create Root for Store 2
        $this->navigate('manage_categories');
        $category = $this->loadDataSet('Category', 'root_category_required');
        $this->categoryHelper()->createCategory($category);
        $this->navigate('manage_stores');

        // Crete Store 2 and Store View 2
        $storeData = $this->loadDataSet('Store', 'generic_store', array('root_category' => $category['name']));
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');

        $storeViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');

        //Create product and assign to Website2
        $this->navigate('manage_products');
        $productData =
            $this->loadDataSet('Product', 'simple_product_url_rewrite', array('categories' => $category['name']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        //Open Manage URL rewrite page
        $this->admin('url_rewrite_management');

        //Click 'Add new rewrite' button
        $this->clickButton('add_new_rewrite');
        $this->waitForAjax();

        //Select "For Product"
        $this->fillDropdown('create_url_rewrite_dropdown', 'For product');
        $this->waitForPageToLoad();

        //Find product in the Grid and open it
        $this->validatePage('add_new_urlrewrite_product');
        $this->searchAndOpen($productSearch, 'product_rewrite', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('product'));
        $this->validatePage();

        //Select Category
        $categorySearch = $productData['categories'];
        $this->addParameter('rootName', $categorySearch);
        $this->clickControl('link', 'root_category', false);
        $this->waitForPageToLoad();
        $this->addParameter('categoryId', $this->defineParameterFromUrl('category'));
        $this->validatePage();

        //Check that 'Dafault Store View' isn\t present in request store
        $options = $this->select($this->getControlElement('dropdown', 'request_store'))->selectOptionLabels();
        if (in_array('Default Store View', $options)) {
            $this->fail('Option with value "Default Store View" is present in "request_store" dropdown');
        }
    }
}
