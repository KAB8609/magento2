<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Legacy tests to find themes non-modular local.xml files declaration
 */
namespace Magento\Test\Legacy;

class ObsoleteThemeLocalXmlTest extends \PHPUnit_Framework_TestCase
{
    public function testLocalXmlFilesAbsent()
    {
        $area = '*';
        $package = '*';
        $theme = '*';
        $this->assertEmpty(glob(
            \Magento\TestFramework\Utility\Files::init()->getPathToSource()
                . "/app/design/{$area}/{$package}/{$theme}/local.xml"
        ));
    }
}