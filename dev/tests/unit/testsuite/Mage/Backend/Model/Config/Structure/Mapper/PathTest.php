<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Mapper_PathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config_Structure_Mapper_Path
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Config_Structure_Mapper_Path();
    }

    public function testMap()
    {
        $data = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_1' => array(
                            'id' => 'section_1',
                            'children' => array(
                                'group_1' => array(
                                    'id' => 'group_1',
                                    'children' => array(
                                        'field_1' => array(
                                            'id' => 'field_1',
                                        ),
                                        'group_1.1' => array(
                                            'id' => 'group_1.1',
                                            'children' => array(
                                                'field_1.2' => array(
                                                    'id' => 'field_1.2',
                                                )
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        $expected = array(
            'config' => array(
                'system' => array(
                    'sections' => array(
                        'section_1' => array(
                            'id' => 'section_1',
                            'children' => array(
                                'group_1' => array(
                                    'id' => 'group_1',
                                    'children' => array(
                                        'field_1' => array(
                                            'id' => 'field_1',
                                            'path' => 'section_1/group_1',
                                        ),
                                        'group_1.1' => array(
                                            'id' => 'group_1.1',
                                            'children' => array(
                                                'field_1.2' => array(
                                                    'id' => 'field_1.2',
                                                    'path' => 'section_1/group_1/group_1.1',
                                                ),
                                            ),
                                            'path' => 'section_1/group_1',
                                        ),
                                    ),
                                    'path' => 'section_1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $actual = $this->_model->map($data);
        $this->assertEquals($expected, $actual);
    }
}
