<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/migration/Acl/Menu/Generator.php';

/**
 * Tools_Migration_Acl_Menu_Generator_Menu generate test case
 */
class Tools_Migration_Acl_Menu_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $model Tools_Migration_Acl_Menu_Generator
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    /**
     * @var array
     */
    protected $_menuFiles = array();

    /**
     * @var array
     */
    protected $_menuIdToXPath = array();


    public function setUp()
    {
        $this->_fixturePath = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . '_files';

        $aclXPathToId = array(
            'config/acl/resources/admin/system' => 'Module_Name::acl_resource',
            'config/acl/resources/admin/area_config/design/node' => 'Module_Name::acl_resource_design',
            'config/acl/resources/admin/area_config' => 'Module_Name::acl_resource_area',
            'config/acl/resources/admin/some_other_resource' => 'Module_Name::some_other_resource',
        );

        $this->_model = new Tools_Migration_Acl_Menu_Generator(
            $this->_fixturePath,
            array(1),
            $aclXPathToId,
            true
        );

        $prefix = $this->_fixturePath . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR;
        $suffix = DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'menu.xml';

        $this->_menuFiles = array(
            $prefix . 'community' . DIRECTORY_SEPARATOR . 'Namespace' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'core' . DIRECTORY_SEPARATOR . 'Enterprise' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'core' . DIRECTORY_SEPARATOR . 'Mage' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'local' . DIRECTORY_SEPARATOR . 'Namespace' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
        );

        $this->_menuIdToXPath = array(
            'Module_Name::system' => '/some/resource',
            'Module_Name::system_config' => 'system/config',
            'Module_Name::area_config_design_node' => 'area_config/design/node',
            'Enterprise_Module::area_config_design' => 'area_config/design',
            'Mage_Module::area_config' => 'area_config',
            'Local_Module::area_config_design_node_email_template' => 'area_config/design/node/email_template',
        );
    }

    public function testGetEtcPattern()
    {
        $path = $this->_fixturePath . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR
            . '*' . DIRECTORY_SEPARATOR
            . '*' . DIRECTORY_SEPARATOR
            . '*' . DIRECTORY_SEPARATOR
            . 'etc' . DIRECTORY_SEPARATOR;

        $this->assertEquals($path, $this->_model->getEtcDirPattern());
    }

    public function testGetMenuFiles()
    {
        $this->assertEquals($this->_menuFiles, $this->_model->getMenuFiles());
    }

    public function testParseMenuNode()
    {
        $menuFile = $this->_menuFiles[0];
        $dom = new DOMDocument();
        $dom->load($menuFile);
        $node = $dom->getElementsByTagName('menu')->item(0);
        $expected = array(
          'Module_Name::system' => array(
              'parent' => '',
              'resource' => '/some/resource',
          ),
          'Module_Name::system_config' => array(
              'parent' => 'Module_Name::system',
              'resource' => '',
          ),
          'Module_Name::area_config_design_node' => array(
              'parent' => 'Enterprise_Module::area_config_design',
              'resource' => '',
          ),
        );

        $this->assertEmpty($this->_model->getMenuIdMaps());
        $this->_model->parseMenuNode($node);
        $this->assertEquals($expected, $this->_model->getMenuIdMaps());
    }

    public function testParseMenuFiles()
    {
        $this->_model->parseMenuFiles();
        /**
         * Check that all nodes from all fixture files were read
         */
        $this->assertEquals(6, count($this->_model->getMenuIdMaps()));

        /**
         * Check that dom list is initialized
         */
        $domList = $this->_model->getMenuDomList();
        $this->assertEquals(4, count($domList));
        $this->assertEquals($this->_menuFiles, array_keys($domList));
        $this->assertInstanceOf('DOMDocument', current($domList));
    }

    public function testInitParentItems()
    {
        $this->_model->parseMenuFiles();
        $menuId = 'Local_Module::area_config_design_node_email_template';

        $maps = $this->_model->getMenuIdMaps();
        $this->assertArrayNotHasKey('parents', $maps[$menuId]);

        $this->_model->initParentItems($menuId);

        $expected = array(
            'Module_Name::area_config_design_node',
            'Enterprise_Module::area_config_design',
            'Mage_Module::area_config',
        );
        $maps = $this->_model->getMenuIdMaps();
        $this->assertEquals($expected, $maps[$menuId]['parents']);
    }

    /**
     * @covers Tools_Migration_Acl_Menu_Generator::buildMenuItemsXPath
     * @covers Tools_Migration_Acl_Menu_Generator::buildXPath
     */
    public function testBuildMenuItemsXPath()
    {
        $this->_model->parseMenuFiles();
        $this->assertEmpty($this->_model->getIdToXPath());

        $this->_model->buildMenuItemsXPath();
        $maps = $this->_model->getIdToXPath();

        $this->assertEquals($this->_menuIdToXPath, $maps);
    }

    public function testMapMenuToAcl()
    {
        $this->assertEmpty($this->_model->getMenuIdToAclId());
        $this->_model->setIdToXPath($this->_menuIdToXPath);
        $result = $this->_model->mapMenuToAcl();
        $map = $this->_model->getMenuIdToAclId();
        $expectedMap = array(
            'Module_Name::area_config_design_node' => 'Module_Name::acl_resource_design',
            'Mage_Module::area_config' => 'Module_Name::acl_resource_area',
        );
        $this->assertEquals($expectedMap, $map);
        $this->assertEquals(array_keys($expectedMap), $result['mapped']);
        $this->assertEquals(4, count($result['not_mapped']));
        $this->assertEquals($expectedMap, json_decode(current($result['artifacts']), true));
    }

    public function testUpdateMenuAttributes()
    {
        $menuFileSource = $this->_fixturePath . DIRECTORY_SEPARATOR . 'update_menu_attributes_source.xml';
        $menuFileResult = $this->_fixturePath . DIRECTORY_SEPARATOR . 'update_menu_attributes_result.xml';

        $domSource = new DOMDocument();
        $domSource->load($menuFileSource);

        $domExpected = new DOMDocument();
        $domExpected->load($menuFileResult);

        $domList = array(
           $menuFileSource => $domSource,
        );
        $menuIdToAclId = array(
            'item1' => 'acl1',
            'item2' => 'acl2',
            'item3' => 'acl3',
        );
        $aclXPathToId = array(
            'config/acl/resources/admin/some/resource' => 'acl4',
            'config/acl/resources/admin/some_other_resource' => 'acl5',
        );
        $this->_model->setMenuDomList($domList);
        $this->_model->setMenuIdToAclId($menuIdToAclId);
        $this->_model->setAclXPathToId($aclXPathToId);

        $errors = $this->_model->updateMenuAttributes();

        $this->assertEquals($domExpected->saveXML(), $domSource->saveXML());
        $this->assertEquals(2, count($errors));

        $this->assertContains('item4 is not mapped', $errors[0]);
        $this->assertContains($menuFileSource, $errors[0]);

        $this->assertContains('no ACL resource with XPath', $errors[1]);
        $this->assertContains($menuFileSource, $errors[1]);
    }
}
