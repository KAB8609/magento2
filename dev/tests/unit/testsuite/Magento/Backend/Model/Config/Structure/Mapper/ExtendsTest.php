<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Mapper;

class ExtendsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Mapper\ExtendsMapper
     */
    protected $_sut;

    public function setUp()
    {
        $this->_sut = new \Magento\Backend\Model\Config\Structure\Mapper\ExtendsMapper(
            new \Magento\Backend\Model\Config\Structure\Mapper\Helper\RelativePathConverter()
        );
    }

    /**
     * @dataProvider testMapDataProvider
     * @param array $sourceData
     * @param array $resultData
     */
    public function testMap($sourceData, $resultData)
    {
        $this->assertEquals($resultData, $this->_sut->map($sourceData));
    }

    public function testMapWithBadPath()
    {
        $this->setExpectedException('InvalidArgumentException',
            'Invalid path in extends attribute of config/system/sections/section1 node');
        $sourceData = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section1' => array(
                            'extends' => 'nonExistentSection2'
                        )
                    )
                )
            )
        );

        $this->_sut->map($sourceData);
    }

    public function testMapDataProvider()
    {
        return array(
            array(array(), array()),
            $this->_emptySectionsNodeData(),
            $this->_extendFromASiblingData(),
            $this->_extendFromNodeOnHigherLevelData(),
            $this->_extendWithMerge()
        );
    }

    protected function _emptySectionsNodeData()
    {
        $data = array (
            'config' => array(
                'system' => array(
                    'sections' => 'some_non_array'
                )
            )
        );

        return array($data, $data);
    }

    protected function _extendFromASiblingData()
    {
        $source = $result = array (
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section1' => array(
                            'children' => array(
                                'child1',
                                'child2',
                                'child3',
                            )
                        ),
                        'section2' => array(
                            'extends' => 'section1'
                        )
                    )
                )
            )
        );

        $result['config']['system']['sections']['section2']['children']
            = $source['config']['system']['sections']['section1']['children'];

        return array($source, $result);
    }

    protected function _extendFromNodeOnHigherLevelData()
    {
        $source = $result = array (
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section1' => array(
                            'children' => array(
                                'child1' => array(
                                    'children' => array(
                                        'subchild1' => 1,
                                        'subchild2' => array(
                                            'extends' => '*/child2'
                                        )
                                    )
                                ),
                                'child2' => array(
                                    'some' => 'Data',
                                    'for' => 'node',
                                    'being' => 'extended'
                                ),
                                'child3' => 3,
                            )
                        ),
                    )
                )
            )
        );

        $result['config']['system']['sections']['section1']['children']['child1']['children']['subchild2']['some']
            = 'Data';
        $result['config']['system']['sections']['section1']['children']['child1']['children']['subchild2']['for']
            = 'node';
        $result['config']['system']['sections']['section1']['children']['child1']['children']['subchild2']['being']
            = 'extended';

        return array($source, $result);
    }

    protected function _extendWithMerge()
    {
        $source = $result = array (
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section1' => array(
                            'scalarValue1' => 1,
                            'children' => array(
                                'child1' => 1,
                                'child2' => 2,
                                'child3' => 3,
                            )
                        ),
                        'section2' => array(
                            'extends' => 'section1',
                            'scalarValue1' => 2,
                            'children' => array (
                                'child4' => 4,
                                'child5' => 5,
                                'child1' => 6
                            )
                        )
                    )
                )
            )
        );

        $section2 = &$result['config']['system']['sections']['section2'];
        $section2['children'] = array(
            'child4' => 4,
            'child5' => 5,
            'child1' => 6,
            'child2' => 2,
            'child3' => 3
        );

        return array($source, $result);
    }
}
