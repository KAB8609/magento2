<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test if category changes through API properly applied on frontend
 */
class Api_Catalog_Category_FrontendTest extends Magento_Test_Webservice
{
    /** @var int Share category id between methods */
    protected static $_categoryId;

    /**
     * Fixture data
     *
     * @var array
     */
    protected $_fixture;

    /**
     * Test if category parameter is_active properly changes on frontend
     *
     * @return void
     */
    public function testCategoryUpdateAppliedOnFrontend()
    {
        $this->markTestIncomplete("Fix fatal error");
        $categoryFixture = $this->_getFixtureData();
        $categoryName = $categoryFixture['create']['categoryData']['name'];
        $data = $categoryFixture['create'];

        $categoryId = $this->call('category.create', $data);
        self::$_categoryId = $categoryId;

        //create
        $categoryCreated = Mage::getModel('Mage_Catalog_Model_Category');
        $categoryCreated->load($categoryId);

        //test
        $runOptions = $this->_dispatch('customer/account/login');
        $this->assertContains($categoryName, $runOptions['response']->getBody());
        //echo $runOptions['response']->getBody();
        $this->assertEquals($categoryId,$categoryCreated->getId());
        $this->assertEquals('1', $categoryCreated['is_active']);

        //update
        $data = $categoryFixture['update'];
        $data['categoryId'] = $categoryId;
        $data['categoryData']['is_active'] = 0;
        //$data['categoryData']['name'] = $categoryName;

        $resultUpdated = $this->call('category.update', $data);

        $this->assertTrue($resultUpdated);
        $categoryUpdated = Mage::getModel('Mage_Catalog_Model_Category');
        $categoryUpdated->load($categoryId);

        //flush helper internal cache that doesn't concern
        Mage::unregister('_helper/Mage_Catalog_Helper_Category');

        //test API response
        $this->assertEquals('0', $categoryUpdated['is_active']);

        //test DB
        $this->assertEquals('0', $this->_getCategory()->getIsActive());

        //test block output
        $html = $this->_getBlockOutput();
        $this->assertNotContains($categoryName, $html);
    }

    /**
     * Retrieve navigation menu block output
     * @return string
     */
    protected function _getBlockOutput()
    {
        $block = Mage::getModel('Mage_Catalog_Block_Navigation');
        $block->setTemplate('catalog/navigation/top.phtml');
        $html = $block->toHtml();

        return $html;
    }

    /**
     * Retrieve category data
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        $categoryId = self::$_categoryId;

        $categoryUpdated = Mage::getModel('Mage_Catalog_Model_Category');
        $categoryUpdated->load($categoryId);

        return $categoryUpdated;
    }

    /**
     * Get run options for controller
     *
     * @return array
     */
    protected function _getRunOptions()
    {
        /**
         * Use run options from bootstrap
         */
        $runOptions = Magento_Test_Bootstrap::getInstance()->getAppOptions();
        $runOptions['request']   = new Magento_Test_Request();
        $runOptions['response']  = new Magento_Test_Response();

        return $runOptions;
    }

    /**
     * Make dispatch
     *
     * @param string $uri
     * @return array
     */
    protected function _dispatch($uri)
    {
        $runOptions = $this->_getRunOptions();

        //Unregister previously registered controller
        Mage::unregister('controller');
        Mage::unregister('application_params');

        $urlData = @parse_url(TESTS_WEBSERVICE_URL);
        $path = isset($urlData['path']) ? $urlData['path'] : '';
        $runOptions['request']->setRequestUri(rtrim($path, '/') . '/' . ltrim($uri, '/'));

        $runCode     = '';
        $runScope    = 'store';
        Mage::run($runCode, $runScope, $runOptions);

        return $runOptions;
    }

    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixtureData()
    {
        if (null === $this->_fixture) {
            $this->_fixture = require dirname(__FILE__) . '/_fixture/categoryData.php';
        }
        return $this->_fixture;
    }

    /**
     * Magic method which run after every test
     *
     * @return void
     */
    public function tearDown()
    {
        $categoryId = self::$_categoryId;
        $categoryDelete = $this->call('category.delete', array('categoryId' => $categoryId));

        $this->assertTrue($categoryDelete);
        $categoryCreated = Mage::getModel('Mage_Catalog_Model_Category');
        $categoryCreated->load($categoryId);
        $this->assertEmpty($categoryCreated->getData());
    }
}
