<?php
/**
 * Unit Test for Magento_Filesystem_Stream_Mode_Zlib
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_Mode_ZlibTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider modesDataProvider
     * @param string $mode
     * @param string $expectedMode
     * @param int $ratio
     * @param string $strategy
     */
    public function testConstructor($mode, $expectedMode, $ratio, $strategy)
    {
        $object = new Magento_Filesystem_Stream_Mode_Zlib($mode);
        $this->assertEquals($expectedMode, $object->getMode());
        $this->assertEquals($ratio, $object->getRatio());
        $this->assertEquals($strategy, $object->getStrategy());
    }

    /**
     * @return array
     */
    public function modesDataProvider()
    {
        return array(
            'w' => array('w', 'w', 1, ''),
            'w+' => array('w+', 'w+', 1, ''),
            'r9' => array('r9', 'r', 9, ''),
            'a+8' => array('a+8', 'a+', 8, ''),
            'wb+7' => array('wb+7', 'wb+', 7, ''),
            'r9f' => array('r9f', 'r', 9, 'f'),
            'a+8h' => array('a+8h', 'a+', 8, 'h'),
            'wb+7f' => array('wb+7f', 'wb+', 7, 'f'),
        );
    }
}
