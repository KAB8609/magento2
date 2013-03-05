<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for obsolete and removed config nodes
 */
class Legacy_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($file)
    {
        $obsoleteNodes = array(
            '/config/global/fieldsets'                 => '',
            '/config/global/cache/betatypes'           => '',
            '/config/admin/fieldsets'                  => '',
            '/config/global/models/*/deprecatedNode'   => '',
            '/config/global/models/*/entities/*/table' => '',
            '/config/global/models/*/class'            => '',
            '/config/global/helpers/*/class'           => '',
            '/config/global/blocks/*/class'            => '',
            '/config/global/models/*/resourceModel'    => '',
            '/config/adminhtml/menu'                   => 'Move them to adminhtml.xml.',
            '/config/adminhtml/acl'                    => 'Move them to adminhtml.xml.',
            '/config/*/events/core_block_abstract_to_html_after' =>
                'Event has been replaced with "core_layout_render_element"',
            '/config/*/events/catalog_controller_product_delete' => '',
            '/config//observers/*/args' => 'This was an undocumented and unused feature in event subscribers',
            '/config/default/design/theme' => 'Relocated to /config/<area>/design/theme',
            '/config/default/web/*/base_js_url' => 'See /config/default/web/*/base_lib_url',
            '/config/default/web/*/base_skin_url' => '',
        );
        $xml = simplexml_load_file($file);
        foreach ($obsoleteNodes as $xpath => $suggestion) {
            $this->assertEmpty(
                $xml->xpath($xpath),
                "Nodes identified by XPath '$xpath' are obsolete. $suggestion"
            );
        }
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('config.xml');
    }
}
