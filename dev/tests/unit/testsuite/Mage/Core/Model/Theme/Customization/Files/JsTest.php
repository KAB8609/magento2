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

/**
 * Test theme js file model
 */
class Mage_Core_Model_Theme_Customization_Files_JsTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareFileName()
    {
        $fileName = 'js_file.js';

        /** @var $jsFile Mage_Core_Model_Theme_Customization_Files_Js */
        $jsFile = $this->getMock(
            'Mage_Core_Model_Theme_Customization_Files_Js', array('_getThemeFileByName', 'getId'), array(), '', false
        );

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);

        $jsFile->expects($this->atLeastOnce())
            ->method('_getThemeFileByName')
            ->will($this->returnValue($jsFile));

        $jsFile->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(1));

        $prepareFileName = new ReflectionMethod($jsFile, '_prepareFileName');
        $prepareFileName->setAccessible(true);
        $result = $prepareFileName->invoke($jsFile, $themeModel, $fileName);
        $this->assertEquals('js_file_1.js', $result);
    }

    public function testSaveDataWithoutData()
    {
        $filesModel = $this->_getMockThemeFile();
        $themeModel = $this->_getMockThemeModel();

        $modelJsFile = $this->getMock(
            'Mage_Core_Model_Theme_Customization_Files_Js',
            array('_delete', '_save'),
            array($filesModel)
        );

        $modelJsFile->expects($this->never())->method('_save');
        $modelJsFile->expects($this->never())->method('_delete');
        $modelJsFile->saveData($themeModel);
    }

    public function testSaveDataWithDelete()
    {
        $jsFilesIdForDelete = array(1, 2, 4, 5);
        $themeJsFilesId = array(1, 2, 3, 4, 5, 6);

        $filesModel = $this->_getMockThemeFile();
        $themeModel = $this->_getMockThemeModel();

        $filesCollection = array();
        foreach ($themeJsFilesId as $fileId) {
            $files = $this->_getMockThemeFile();
            $files->expects(in_array($fileId, $jsFilesIdForDelete) ? $this->once() : $this->never())->method('delete');
            $files->expects($this->once())->method('getId')->will($this->returnValue($fileId));
            $filesCollection[] = $files;
        }

        /** @var $modelJsFile Mage_Core_Model_Theme_Customization_Files_Js  */
        $modelJsFile = $this->getMock(
            'Mage_Core_Model_Theme_Customization_Files_Js',
            array('getCollectionByTheme', '_save'),
            array($filesModel)
        );

        $modelJsFile->expects($this->never())->method('_save');
        $modelJsFile->expects($this->once())
            ->method('getCollectionByTheme')
            ->with($themeModel)
            ->will($this->returnValue($filesCollection));

        $modelJsFile->setDataForDelete($jsFilesIdForDelete);
        $modelJsFile->saveData($themeModel);
    }

    /**
     * @param int $return
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme
     */
    protected function _getMockThemeModel($return = null)
    {
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $themeModel->expects($return ? $this->once() : $this->never())
            ->method('getId')
            ->will($this->returnValue($return));
        return $themeModel;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Theme_Files
     */
    protected function _getMockThemeFile()
    {
        $filesModel = $this->getMock('Mage_Core_Model_Theme_Files', array(
            'load',
            'getId',
            'getThemeId',
            'setIsTemporary',
            'save',
            'delete'
        ), array(), '', false);
        return $filesModel;
    }

    /**
     * @param array $items
     * @param array $jsOrderData
     * @param array $expectedResult
     * @dataProvider saveDataWithReorderingDataProvider
     */
    public function testSaveDataWithReordering(array $items, array $jsOrderData, array $expectedResult)
    {
        // 1. Define test data
        $themeId = 1;

        // 2. Get theme mock
        $themeModel = $this->_getMockThemeModel($themeId);

        // 3. Get files collection mock
        /** @var $collection Mage_Core_Model_Resource_Theme_Files_Collection */
        $collection = $this->getMock('Mage_Core_Model_Resource_Theme_Files_Collection',
            array('addFilter', 'setDefaultOrder', 'load', 'save', 'getSize'), array(), '', false
        );
        $this->_addItems($collection, $items);

        $collection->expects($this->any())
            ->method('addFilter')
            ->will($this->returnSelf());
        $collection->expects($this->any())
            ->method('setDefaultOrder')
            ->will($this->returnSelf());

        // 4. Get files model (storage) mock
        /** @var $themeFiles Mage_Core_Model_Theme_Files */
        $themeFiles = $this->getMock('Mage_Core_Model_Theme_Files', array('getCollection'), array(), '', false);
        $themeFiles->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($collection));

        // 5. Create tested class and set test data
        $jsFilesManager = new Mage_Core_Model_Theme_Customization_Files_Js($themeFiles);
        $jsFilesManager->setJsOrderData($jsOrderData);

        // 6. Run tested functionality
        $jsFilesManager->saveData($themeModel);

        // 7. Check results
        $result = $collection->toArray();
        $this->assertEquals(count($result['items']), count($expectedResult['items']));
        foreach ($result['items'] as $item) {
            $this->assertContains($item, $expectedResult['items']);
        }


    }

    /**
     * @return array
     */
    public function saveDataWithReorderingDataProvider()
    {
        return array(
            // case 1
            array(
                array(
                    array('file_name' => '1.js', 'order' => '123'),
                    array('file_name' => '2.js', 'order' => '0'),
                    array('file_name' => '3.js', 'order' => '456')
                ),
                array('1.js', '2.js', '3.js'),
                array(
                    'totalRecords' => null,
                    'items'        => array(
                        array(
                         'file_name' => '1.js',
                         'order'     => 1
                        ),
                        array(
                         'file_name' => '2.js',
                         'order'     => 2
                        ),
                        array(
                         'file_name' => '3.js',
                         'order'     => 3
                        ),
                    )
                )
            ),
            // case 2
            array(
                array(
                    array('file_name' => '3.js', 'order' => '0'),
                    array('file_name' => '2.js', 'order' => '0'),
                    array('file_name' => '1.js', 'order' => '0')
                ),
                array('1.js', '2.js', '3.js'),
                array(
                    'totalRecords' => null,
                    'items'        => array(
                        array(
                         'file_name' => '1.js',
                         'order'     => 1
                        ),
                        array(
                         'file_name' => '2.js',
                         'order'     => 2
                        ),
                        array(
                         'file_name' => '3.js',
                         'order'     => 3
                        ),
                    )
                )
            ),
        );
    }

    /**
     * Set items to files collection
     *
     * @param Mage_Core_Model_Resource_Theme_Files_Collection $collection
     * @param array $items
     */
    protected function _addItems($collection, array $items)
    {
        foreach ($items as $item) {
            $collection->addItem(new Varien_Object($item));
        }
    }
}


