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

class Mage_Core_Model_Theme_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_File
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    protected function setUp()
    {
        $this->_model = Mage::getObjectManager()->create('Mage_Core_Model_Theme_File');
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $this->_theme = $themeModel->getCollection()->getFirstItem();
        $this->_data = array(
            'file_path' => 'main.css',
            'file_type' => 'css',
            'content'   => 'content files',
            'order'     => 0,
            'theme'     => $this->_theme,
            'theme_id'  => $this->_theme->getId(),
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_data = array();
        $this->_theme = null;
    }

    /**
     * Test crud operations for theme files model using valid data
     */
    public function testCrud()
    {
        $this->_model->setData($this->_data);

        $crud = new Magento_Test_Entity($this->_model, array('file_path' => 'rename.css'));
        $crud->testCrud();
    }

    public function testGetFullPath()
    {
        $this->assertNull($this->_model->getFullPath());

        $this->_model->setData($this->_data);
        $this->_model->setId('test');
        $this->assertStringEndsWith('main.css', $this->_model->getFullPath());
    }

    public function testGetAsset()
    {
        $this->assertNull($this->_model->getAsset());
        $this->_model->setData($this->_data);
        $this->_model->setId('test');

        $asset = $this->_model->getAsset();
        $this->assertInstanceOf('Mage_Core_Model_Page_Asset_PublicFile', $asset);
        $assetTwo = $this->_model->getAsset();
        $this->assertNotSame($asset, $assetTwo, '"getAsset()" must return new instance every time');
    }
}
