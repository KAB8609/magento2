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
 * Legacy tests to find obsolete menu declaration
 */
class Legacy_ObsoleteAclTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $aclFile
     * @dataProvider aclFilesDataProvider
     */
    public function testAclDeclaration($aclFile)
    {
        $aclXml = simplexml_load_file($aclFile);
        $xpath = '/config/acl/*[boolean(./children) or boolean(./title)]';
        $this->assertEmpty(
            $aclXml->xpath($xpath),
            'Obsolete acl structure detected in file ' . $aclFile . '.'
        );
    }

    /**
     * @return array
     */
    public function aclFilesDataProvider()
    {
        return Utility_Files::init()->getConfigFiles();
    }
}
