<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once __DIR__ . '/../../../../../../tools/view/Generator/CopyRule.php';

class Tools_View_Generator_CopyRuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Generator_CopyRule
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fallbackRule;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento_Filesystem', array('searchKeys', 'isDirectory'), array(
            $this->getMockForAbstractClass('Magento_Filesystem_AdapterInterface')
        ));
        $this->_themeCollection = $this->getMock('Mage_Core_Model_Theme_Collection', array('isLoaded'), array(
            $this->_filesystem,
            $this->getMockForAbstractClass('Magento_ObjectManager'),
            new Mage_Core_Model_Dir($this->_filesystem, __DIR__)
        ));
        $this->_themeCollection->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $this->_fallbackRule = $this->getMockForAbstractClass('Mage_Core_Model_Design_Fallback_Rule_RuleInterface');
        $this->_object = new Generator_CopyRule($this->_filesystem, $this->_themeCollection, $this->_fallbackRule);
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_filesystem = null;
        $this->_themeCollection = null;
        $this->_fallbackRule = null;
    }

    /**
     * @param array $fixtureThemes
     * @param array $patternDirMap
     * @param array $filesystemGlobMap
     * @param array $expectedResult
     * @dataProvider getCopyRulesDataProvider
     */
    public function testGetCopyRules(
        array $fixtureThemes, array $patternDirMap, array $filesystemGlobMap, array $expectedResult
    ) {
        foreach ($fixtureThemes as $theme) {
            $this->_themeCollection->addItem($theme);
        }
        $this->_fallbackRule
            ->expects($this->atLeastOnce())
            ->method('getPatternDirs')
            ->will($this->returnValueMap($patternDirMap))
        ;
        $this->_filesystem
            ->expects($this->atLeastOnce())
            ->method('searchKeys')
            ->will($this->returnValueMap($filesystemGlobMap))
        ;
        $this->_filesystem
            ->expects($this->atLeastOnce())
            ->method('isDirectory')
            ->will($this->returnValue(true))
        ;
        $actualResult = array();
        foreach ($this->_object->getCopyRules() as $actualCopyRule) {
            unset($actualCopyRule['path_info']);
            $actualResult[] = $actualCopyRule;
        }
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getCopyRulesDataProvider()
    {
        $fixture = require __DIR__ . '/_files/fixture_themes.php';

        $patternDirMap = array();
        $filesystemGlobMap = array();
        foreach ($fixture as $fixtureInfo) {
            $patternDirMap[] = $fixtureInfo['pattern_dir_map'];
            $filesystemGlobMap[] = $fixtureInfo['filesystem_glob_map'];
        }

        return array(
            'reverse fallback traversal' => array(
                array($fixture['fixture_one']['theme']),
                $patternDirMap,
                $filesystemGlobMap,
                $fixture['fixture_one']['expected_result'],
            ),
            'themes in the same area' => array(
                array($fixture['fixture_one']['theme'], $fixture['fixture_two']['theme']),
                $patternDirMap,
                $filesystemGlobMap,
                array_merge($fixture['fixture_one']['expected_result'], $fixture['fixture_two']['expected_result']),
            ),
            'themes in different areas' => array(
                array($fixture['fixture_one']['theme'], $fixture['fixture_three']['theme']),
                $patternDirMap,
                $filesystemGlobMap,
                array_merge($fixture['fixture_one']['expected_result'], $fixture['fixture_three']['expected_result']),
            ),
        );
    }
}
