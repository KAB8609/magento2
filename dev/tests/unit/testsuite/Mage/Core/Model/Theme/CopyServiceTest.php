<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Theme_CopyServiceTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sourceTheme;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_targetTheme;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_link;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_linkCollection;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_update;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_updateCollection;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_updateFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customizationPath;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $_targetFiles = array();

    /**
     * @var PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $_sourceFiles = array();

    protected function setUp()
    {
        $sourceFileOne = $this->getMock('Mage_Core_Model_Theme_File', array('delete'), array(), '', false);
        $sourceFileOne->setData(array(
            'file_path'     => 'fixture_file_path_one',
            'file_type'     => 'fixture_file_type_one',
            'content'       => 'fixture_content_one',
            'sort_order'    => 10,
        ));
        $sourceFileTwo = $this->getMock('Mage_Core_Model_Theme_File', array('delete'), array(), '', false);
        $sourceFileTwo->setData(array(
            'file_path'     => 'fixture_file_path_two',
            'file_type'     => 'fixture_file_type_two',
            'content'       => 'fixture_content_two',
            'sort_order'    => 20,
        ));
        $this->_sourceFiles = array($sourceFileOne, $sourceFileTwo);
        $this->_sourceTheme = $this->getMock(
            'Mage_Core_Model_Theme', array('getFiles', 'getCustomizationPath'), array(), '', false
        );

        $this->_targetFiles = array(
            $this->getMock('Mage_Core_Model_Theme_File', array('delete'), array(), '', false),
            $this->getMock('Mage_Core_Model_Theme_File', array('delete'), array(), '', false),
        );
        $this->_targetTheme = $this->getMock('Mage_Core_Model_Theme', array('getFiles'), array(), '', false);
        $this->_targetTheme->setId(123);

        $this->_customizationPath = $this->getMock('Mage_Core_Model_Theme_Customization_Path',
            array(), array(), '', false);

        $this->_fileFactory = $this->getMock('Mage_Core_Model_Theme_FileFactory', array('create'), array(), '', false);
        $this->_filesystem = $this->getMock(
            'Magento_Filesystem', array('isDirectory', 'searchKeys', 'copy', 'delete'),
            array($this->getMockForAbstractClass('Magento_Filesystem_AdapterInterface'))
        );

        /* Init Mage_Core_Model_Resource_Layout_Collection model  */
        $this->_updateFactory = $this->getMock('Mage_Core_Model_Layout_UpdateFactory', array('create'),
            array(), '', false);
        $this->_update = $this->getMock('Mage_Core_Model_Layout_Update', array('getCollection'), array(), '', false);
        $this->_updateFactory->expects($this->at(0))->method('create')->will($this->returnValue($this->_update));
        $this->_updateCollection = $this->getMock('Mage_Core_Model_Resource_Layout_Collection',
            array('addThemeFilter', 'delete', 'getIterator'), array(), '', false);
        $this->_update->expects($this->any())->method('getCollection')
            ->will($this->returnValue($this->_updateCollection));

        /* Init Link an Link_Collection model */
        $this->_link = $this->getMock('Mage_Core_Model_Layout_Link', array('getCollection'), array(), '', false);
        $this->_linkCollection = $this->getMock('Mage_Core_Model_Resource_Layout_Link_Collection',
            array('addThemeFilter', 'getIterator'), array(), '', false);
        $this->_link->expects($this->any())->method('getCollection')->will($this->returnValue($this->_linkCollection));

        $eventManager = $this->getMock('Mage_Core_Model_Event_Manager', array('dispatch'), array(), '', false);

        $this->_object = new Mage_Core_Model_Theme_CopyService(
            $this->_filesystem,
            $this->_fileFactory,
            $this->_link,
            $this->_updateFactory,
            $eventManager,
            $this->_customizationPath
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_filesystem = null;
        $this->_fileFactory = null;
        $this->_sourceTheme = null;
        $this->_targetTheme = null;
        $this->_link = null;
        $this->_linkCollection = null;
        $this->_updateCollection = null;
        $this->_updateFactory = null;
        $this->_sourceFiles = array();
        $this->_targetFiles = array();
    }

    /**
     * @covers Mage_Core_Model_Theme_CopyService::_copyLayoutCustomization
     */
    public function testCopyLayoutUpdates()
    {
        $this->_sourceTheme->expects($this->once())->method('getFiles')->will($this->returnValue(array()));
        $this->_targetTheme->expects($this->once())->method('getFiles')->will($this->returnValue(array()));

        $this->_updateCollection->expects($this->once())->method('delete');
        $this->_linkCollection->expects($this->once())->method('addThemeFilter');

        $targetLinkOne = $this->getMock('Mage_Core_Model_Layout_Link',
            array('setId', 'setThemeId', 'save', 'setLayoutUpdateId'), array(), '', false);
        $targetLinkOne->setData(array('id' => 1, 'layout_update_id' => 1));
        $targetLinkTwo = $this->getMock('Mage_Core_Model_Layout_Link',
            array('setId', 'setThemeId', 'save', 'setLayoutUpdateId'), array(), '', false);
        $targetLinkTwo->setData(array('id' => 2, 'layout_update_id' => 2));

        $targetLinkOne->expects($this->at(0))->method('setThemeId')->with(123);
        $targetLinkOne->expects($this->at(1))->method('setLayoutUpdateId')->with(1);
        $targetLinkOne->expects($this->at(2))->method('setId')->with(null);
        $targetLinkOne->expects($this->at(3))->method('save');

        $targetLinkTwo->expects($this->at(0))->method('setThemeId')->with(123);
        $targetLinkTwo->expects($this->at(1))->method('setLayoutUpdateId')->with(2);
        $targetLinkTwo->expects($this->at(2))->method('setId')->with(null);
        $targetLinkTwo->expects($this->at(3))->method('save');

        $linkReturnValues = $this->onConsecutiveCalls(new ArrayIterator(array($targetLinkOne, $targetLinkTwo)));
        $this->_linkCollection->expects($this->any())->method('getIterator')->will($linkReturnValues);

        $targetUpdateOne = $this->getMock('Mage_Core_Model_Layout_Update', array('setId', 'load', 'save'),
            array(), '', false);
        $targetUpdateOne->setData(array('id' => 1));
        $targetUpdateTwo = $this->getMock('Mage_Core_Model_Layout_Update', array('setId', 'load', 'save'),
            array(), '', false);
        $targetUpdateTwo->setData(array('id' => 2));
        $updateReturnValues = $this->onConsecutiveCalls($this->_update, $targetUpdateOne, $targetUpdateTwo);
        $this->_updateFactory->expects($this->any())->method('create')->will($updateReturnValues);

        $this->_object->copy($this->_sourceTheme, $this->_targetTheme);
    }

    /**
     * @covers Mage_Core_Model_Theme_CopyService::_copyDatabaseCustomization
     */
    public function testCopyDatabaseCustomization()
    {
        $this->_sourceTheme->expects($this->once())->method('getFiles')->will($this->returnValue($this->_sourceFiles));
        $this->_targetTheme->expects($this->once())->method('getFiles')->will($this->returnValue($this->_targetFiles));

        $this->_linkCollection->expects($this->any())->method('addFieldToFilter')
            ->will($this->returnValue($this->_linkCollection));
        $this->_linkCollection->expects($this->any())->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array())));

        foreach ($this->_targetFiles as $targetFile) {
            $targetFile->expects($this->once())->method('delete');
        }

        $newFileOne = $this->getMock('Mage_Core_Model_Theme_File', array('setData', 'save'), array(), '', false);
        $newFileTwo = $this->getMock('Mage_Core_Model_Theme_File', array('setData', 'save'), array(), '', false);
        $newFileOne->expects($this->at(0))->method('setData')->with(array(
            'theme_id'      => 123,
            'file_path'     => 'fixture_file_path_one',
            'file_type'     => 'fixture_file_type_one',
            'content'       => 'fixture_content_one',
            'sort_order'    => 10,
        ));
        $newFileOne->expects($this->at(1))->method('save');
        $newFileTwo->expects($this->at(0))->method('setData')->with(array(
            'theme_id'      => 123,
            'file_path'     => 'fixture_file_path_two',
            'file_type'     => 'fixture_file_type_two',
            'content'       => 'fixture_content_two',
            'sort_order'    => 20,
        ));
        $newFileTwo->expects($this->at(1))->method('save');
        $this->_fileFactory
            ->expects($this->any())
            ->method('create')
            ->with(array())
            ->will($this->onConsecutiveCalls($newFileOne, $newFileTwo))
        ;

        $this->_object->copy($this->_sourceTheme, $this->_targetTheme);
    }

    /**
     * @covers Mage_Core_Model_Theme_CopyService::_copyFilesystemCustomization
     */
    public function testCopyFilesystemCustomization()
    {
        $this->_sourceTheme->expects($this->once())->method('getFiles')->will($this->returnValue(array()));
        $this->_targetTheme->expects($this->once())->method('getFiles')->will($this->returnValue(array()));

        $this->_linkCollection->expects($this->any())->method('addFieldToFilter')
            ->will($this->returnValue($this->_linkCollection));
        $this->_linkCollection->expects($this->any())->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array())));

        $this->_sourceTheme
            ->expects($this->once())->method('getCustomizationPath')->will($this->returnValue('source/path'));

        $this->_targetTheme
            ->expects($this->once())->method('getCustomizationPath')->will($this->returnValue('target/path'));

        $this->_filesystem->expects($this->any())
            ->method('isDirectory')->will($this->returnValueMap(array(
                array('source/path', null, true),
            )));

        $this->_filesystem
            ->expects($this->any())
            ->method('searchKeys')
            ->will($this->returnValueMap(array(
                array('target/path', '*', array()),
                array('source/path', '*', array('source/path/file_one.jpg', 'source/path/file_two.png'))
            )));

        $expectedCopyEvents = array(
            array('source/path/file_one.jpg', 'target/path/file_one.jpg', 'source/path', 'target/path'),
            array('source/path/file_two.png', 'target/path/file_two.png', 'source/path', 'target/path'),
        );
        $actualCopyEvents = array();
        $recordCopyEvent = function () use (&$actualCopyEvents) {
            $actualCopyEvents[] = func_get_args();
        };
        $this->_filesystem->expects($this->any())->method('copy')->will($this->returnCallback($recordCopyEvent));

        $this->_object->copy($this->_sourceTheme, $this->_targetTheme);

        $this->assertEquals($expectedCopyEvents, $actualCopyEvents);
    }
}
