<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '../../../../../../../../') . '/tools/migration/Acl/Db/FileReader.php';

class Tools_Migration_Acl_Db_FileReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_Acl_Db_FileReader
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Tools_Migration_Acl_Db_FileReader();
    }

    public function testExtractData()
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . '../_files' . DIRECTORY_SEPARATOR . 'log'
            . DIRECTORY_SEPARATOR . 'AclXPathToAclId.log';
        $expectedMap = array(
            "admin/test1/test2"        => "Test1_Test2::all",
            "admin/test1/test2/test3"  => "Test1_Test2::test3",
            "admin/test1/test2/test4"  => "Test1_Test2::test4",
            "admin/test1/test2/test5"  => "Test1_Test2::test5",
            "admin/test6"              => "Test6_Test6::all"
        );
        $this->assertEquals($expectedMap, $this->_model->extractData($filePath));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExtractDataThrowsExceptionIfInvalidFileProvided()
    {
        $this->_model->extractData('invalidFile.log');
    }
}

