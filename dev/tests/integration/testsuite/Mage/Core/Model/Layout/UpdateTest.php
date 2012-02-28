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
 * @group module:Mage_Core
 */
class Mage_Core_Model_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Update
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => dirname(__DIR__) . '/_files/design',
        ));
        Mage::getDesign()->setDesignTheme('test/default/default');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout_Update();
    }

    public function testGetElementClass()
    {
        $this->assertEquals('Mage_Core_Model_Layout_Element', $this->_model->getElementClass());
    }

    public function testUpdates()
    {
        $this->assertEmpty($this->_model->asArray());
        $this->_model->addUpdate('test1');
        $this->assertEquals(array('test1'), $this->_model->asArray());
        $this->assertEquals('test1', $this->_model->asString());
    }

    public function testHandles()
    {
        $this->assertEmpty($this->_model->getHandles());
        $this->_model->addHandle('test');
        $this->assertEquals(array('test'), $this->_model->getHandles());
        $this->_model->removeHandle('test');
        $this->assertEmpty($this->_model->getHandles());
    }

    public function testAddPageHandles()
    {
        /* add a non-page handle to verify that it won't be affected during page handles manipulation */
        $nonPageHandles = array('non_page_handle');
        $this->_model->addHandle($nonPageHandles);

        $this->assertFalse($this->_model->addPageHandles(array('non_existing_handle')));
        $this->assertEmpty($this->_model->getPageHandles());
        $this->assertEquals($nonPageHandles, $this->_model->getHandles());

        /* test that only the first existing handle is taken into account */
        $handlesToTry = array('catalog_product_view_type_simple', 'catalog_category_view');
        $expectedPageHandles = array('default', 'catalog_product_view', 'catalog_product_view_type_simple');
        $this->assertTrue($this->_model->addPageHandles($handlesToTry));
        $this->assertEquals($expectedPageHandles, $this->_model->getPageHandles());
        $this->assertEquals(array_merge($nonPageHandles, $expectedPageHandles), $this->_model->getHandles());

        /* test that new handles override the previous ones */
        $expectedPageHandles = array('default', 'catalog_category_view', 'catalog_category_view_type_default');
        $this->assertTrue($this->_model->addPageHandles(array('catalog_category_view_type_default')));
        $this->assertEquals($expectedPageHandles, $this->_model->getPageHandles());
        $this->assertEquals(array_merge($nonPageHandles, $expectedPageHandles), $this->_model->getHandles());
    }

    /**
     * @dataProvider getPageLayoutHandlesDataProvider
     */
    public function testGetPageLayoutHandles($inputPageHandle, $expectedResult)
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_page_types.xml');
        $this->assertSame($expectedResult, $model->getPageLayoutHandles($inputPageHandle));
    }

    public function getPageLayoutHandlesDataProvider()
    {
        return array(
            'non-existing handle'      => array('non_existing_handle', array()),
            'non page type handle'     => array('not_a_page_type', array()),
            'page type with no parent' => array('default', array('default')),
            'page type with parent'    => array(
                'catalog_category_default', array('default', 'catalog_category_default')
            ),
            'deeply nested page type'  => array(
                'catalog_category_layered', array('default', 'catalog_category_default', 'catalog_category_layered')
            ),
        );
    }

    public function testGetPageTypesHierarchy()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_page_types.xml');
        $expected = require(__DIR__ . '/_files/_page_types_hierarchy.php');
        $actual = $model->getPageTypesHierarchy();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider pageTypeExistsDataProvider
     */
    public function testPageTypeExists($inputPageType, $expectedResult)
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_page_types.xml');
        $this->assertSame($expectedResult, $model->pageTypeExists($inputPageType));
    }

    public function pageTypeExistsDataProvider()
    {
        return array(
            'non-existing handle'  => array('non_existing_handle', false),
            'non page type handle' => array('not_a_page_type',     false),
            'existing page type'   => array('default',             true),
        );
    }

    /**
     * @dataProvider getPageTypeLabelDataProvider
     */
    public function testGetPageTypeLabel($inputPageType, $expectedResult)
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_page_types.xml');
        $this->assertSame($expectedResult, $model->getPageTypeLabel($inputPageType));
    }

    public function getPageTypeLabelDataProvider()
    {
        return array(
            'non-existing handle'  => array('non_existing_handle', false),
            'non page type handle' => array('not_a_page_type',     false),
            'existing page type'   => array('default',             'All Pages'),
        );
    }

    /**
     * @dataProvider getPageTypeParentDataProvider
     */
    public function testGetPageTypeParent($inputPageType, $expectedResult)
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);
        $model = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_page_types.xml');
        $this->assertSame($expectedResult, $model->getPageTypeParent($inputPageType));
    }

    public function getPageTypeParentDataProvider()
    {
        return array(
            'non-existing handle'      => array('non_existing_handle',      false),
            'non page type handle'     => array('not_a_page_type',          false),
            'page type with no parent' => array('default',                  null),
            'page type with parent'    => array('catalog_category_default', 'default'),
            'deeply nested page type'  => array('catalog_category_layered', 'catalog_category_default'),
        );
    }

    /**
     * @magentoDataFixture Mage/Core/Model/Layout/_files/db_layout_update.php
     */
    public function testFetchDbLayoutUpdates()
    {
        $this->_model->fetchDbLayoutUpdates('fixture_handle');
        $this->assertStringMatchesFormat(
            '<reference name="root">%w<block type="Mage_Core_Block_Template" template="dummy.phtml"/>%w</reference>',
            trim($this->_model->asString())
        );
    }

    public function testGetFileLayoutUpdatesXmlFromTheme()
    {
        $this->_replaceConfigLayoutUpdates('
            <core module="Mage_Core">
                <file>layout.xml</file>
            </core>
        ');
        $expectedXmlStr = $this->_readLayoutFileContents(
            __DIR__ . '/../_files/design/frontend/test/default/Mage_Core/layout.xml'
        );
        $actualXml = $this->_model->getFileLayoutUpdatesXml('frontend', 'test', 'default');
        $this->assertXmlStringEqualsXmlString($expectedXmlStr, $actualXml->asNiceXml());
    }

    public function testGetFileLayoutUpdatesXmlFromModule()
    {
        $this->_replaceConfigLayoutUpdates('
            <page module="Mage_Page">
                <file>layout.xml</file>
            </page>
        ');
        $expectedXmlStr = $this->_readLayoutFileContents(
            __DIR__ . '/../../../../../../../../app/code/core/Mage/Page/view/frontend/layout.xml'
        );
        $actualXml = $this->_model->getFileLayoutUpdatesXml('frontend', 'test', 'default');
        $this->assertXmlStringEqualsXmlString($expectedXmlStr, $actualXml->asNiceXml());
    }

    /**
     * Replace configuration XML node <area>/layout/updates with the desired content
     *
     * @param string $replacementXmlStr
     * @param string $area
     */
    protected function _replaceConfigLayoutUpdates($replacementXmlStr, $area = 'frontend')
    {
        /* Erase existing layout updates */
        unset(Mage::app()->getConfig()->getNode("{$area}/layout")->updates);
        /* Setup layout updates fixture */
        Mage::app()->getConfig()->extend(new Varien_Simplexml_Config("
            <config>
                <{$area}>
                    <layout>
                        <updates>
                            {$replacementXmlStr}
                        </updates>
                    </layout>
                </{$area}>
            </config>
        "));
    }

    /**
     * Retrieve contents of the layout update file, preprocessed to be comparable with the merged layout data
     *
     * @param string $filename
     * @return string
     */
    protected function _readLayoutFileContents($filename)
    {
        /* Load & render XML to get rid of comments and replace root node name from <layout> to <layouts> */
        $xml = simplexml_load_file($filename, 'Varien_Simplexml_Element');
        $text = '';
        foreach ($xml->children() as $child) {
            $text .= $child->asNiceXml();
        }
        return '<layouts>' . $text . '</layouts>';
    }

    /**
     * @expectedException Magento_Exception
     * @dataProvider getFileLayoutUpdatesXmlExceptionDataProvider
     */
    public function testGetFileLayoutUpdatesXmlException($configFixture)
    {
        $this->_replaceConfigLayoutUpdates($configFixture);
        $this->_model->getFileLayoutUpdatesXml('frontend', 'test', 'default');
    }

    public function getFileLayoutUpdatesXmlExceptionDataProvider()
    {
        return array(
            'non-existing layout file' => array('
                <core module="Mage_Core">
                    <file>non_existing_layout.xml</file>
                </core>
            '),
            'module attribute absence' => array('
                <core>
                    <file>layout.xml</file>
                </core>
            '),
            'non-existing module'      => array('
                <core module="Non_ExistingModule">
                    <file>layout.xml</file>
                </core>
            '),
        );
    }

    /**
     * @magentoConfigFixture current_store advanced/modules_disable_output/Mage_Catalog true
     * @magentoConfigFixture current_store advanced/modules_disable_output/Mage_Page    true
     */
    public function testGetFileLayoutUpdatesXmlDisabledOutput()
    {
        $this->_replaceConfigLayoutUpdates('
            <catalog module="Mage_Catalog">
                <file>layout.xml</file>
            </catalog>
            <core module="Mage_Core">
                <file>layout.xml</file>
            </core>
            <page module="Mage_Page">
                <file>layout.xml</file>
            </page>
        ');
        $expectedXmlStr = $this->_readLayoutFileContents(
            __DIR__ . '/../_files/design/frontend/test/default/Mage_Core/layout.xml'
        );
        $actualXml = $this->_model->getFileLayoutUpdatesXml('frontend', 'test', 'default');
        $this->assertXmlStringEqualsXmlString($expectedXmlStr, $actualXml->asNiceXml());
    }
}
