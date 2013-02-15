<?php
/**
 * Test API data helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Api_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Api_Helper_Data */
    protected $_helper;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    public function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array('getPathInfo'));
        $this->_helper = new Mage_Api_Helper_Data($this->_requestMock);
    }

    /**
     * Check if it is possible to use several filters by the same field. Non WS-I compatible mode.
     *
     * @dataProvider providerParseFiltersMultipleConditionsOnSameField
     */
    public function testParseFiltersMultipleConditionsOnSameField($input, $expectedOutput)
    {
        $this->_requestMock->expects($this->any())->method('getPathInfo')->will($this->returnValue('api/soap'));
        $output = $this->_helper->parseFilters($input);
        $this->assertEquals(
            $expectedOutput,
            $output,
            "Multiple filters by the same field are not processed correctly in non WS-I mode."
        );
    }

    /**
     * Data provider for both WS-I and non WS-I modes.
     *
     * @return array
     */
    public static function providerParseFiltersMultipleConditionsOnSameField()
    {
        return array(
            array(
                (object)array(
                    'complex_filter' => array(
                        (object)array(
                            'key' => 'created_at',
                            'value' => (object)array('key' => 'from', 'value' => '2000-01-01 00:00:00')
                        ),
                        (object)array(
                            'key' => 'created_at',
                            'value' => (object)array('key' => 'to', 'value' => '2001-01-01 00:00:00')
                        ),
                        (object)array(
                            'key' => 'updated_at',
                            'value' => (object)array('key' => 'gt', 'value' => '2002-01-01 00:00:00')
                        ),
                    )
                ),
                array(
                    'created_at' => array('from' => '2000-01-01 00:00:00', 'to' => '2001-01-01 00:00:00'),
                    'updated_at' => array('gt' => '2002-01-01 00:00:00')
                )
            )
        );
    }

    /**
     * Check if it is possible to use several filters by the same field. WS-I compatible mode.
     *
     * @dataProvider providerParseFiltersMultipleConditionsOnSameField
     */
    public function testParseFiltersMultipleConditionsOnSameFieldWsi($input, $expectedOutput)
    {
        $this->markTestIncomplete('MAGETWO-7571');
        $this->_requestMock->expects($this->any())->method('getPathInfo')->will($this->returnValue('api/soap_wsi'));
        $output = $this->_helper->parseFilters($input);
        $this->assertEquals(
            $expectedOutput,
            $output,
            "Multiple filters by the same field are not processed correctly in WS-I mode."
        );
    }

    /**
     * Test simple filter WS-I unpacking.
     *
     * @dataProvider providerWsiArrayUnpackerWithFilter
     */
    public function testWsiArrayUnpackerWithFilter($rawWsiData, $expectedUnpackedData)
    {
        /** Execute process data with tested method by reference. */
        $this->_helper->wsiArrayUnpacker($rawWsiData);
        $unpackedData = $rawWsiData;

        $this->assertEquals(
            $unpackedData,
            $expectedUnpackedData,
            'Simple filters were formatted incorrectly.'
        );
    }

    public static function providerWsiArrayUnpackerWithFilter()
    {
        return array(
            /** Case with several simple filters applied */
            array(
                (object)array(
                    'sessionId' => 'session',
                    'filters' => (object)array(
                        'filter' => (object)array(
                            'complexObjectArray' => array(
                                (object)array('key' => 'order_id', 'value' => 1),
                                (object)array('key' => 'status', 'value' => 2)
                            )
                        )
                    )
                ),
                (object)array(
                    'sessionId' => 'session',
                    'filters' => (object)array(
                        'filter' => array(
                            'order_id' => 1,
                            'status' => 2
                        )
                    )
                )
            ),
            /** Case with single simple filter applied */
            array(
                (object)array(
                    'sessionId' => 'session',
                    'filters' => (object)array(
                        'filter' => (object)array(
                            'complexObjectArray' => (object)array('key' => 'order_id', 'value' => 1)
                        )
                    )
                ),
                (object)array(
                    'sessionId' => 'session',
                    'filters' => (object)array(
                        'filter' => array(
                            'order_id' => 1
                        )
                    )
                )
            )
        );
    }

    /**
     * Test complex filter WS-I unpacking.
     */
    public function testWsiArrayUnpackerWithComplexFilter()
    {
        $this->markTestIncomplete('MAGETWO-7571');
        $rawWsiData = (object)array(
            'sessionId' => 'session',
            'filters' => (object)array(
                'complex_filter' => (object)array(
                    'complexObjectArray' => array(
                        (object)array('key' => 'order_id', 'value' => (object)array('key' => 'gt', 'value' => 1)),
                        (object)array('key' => 'order_id', 'value' => (object)array('key' => 'lt', 'value' => 5)),
                        (object)array('key' => 'status', 'value' => (object)array('key' => 'eq', 'value' => 2))
                    )
                )
            )
        );
        $expectedUnpackedData = (object)array(
            'sessionId' => 'session',
            'filters' => (object)array(
                'complex_filter' => array(
                    (object)array('key' => 'order_id', 'value' => (object)array('key' => 'gt', 'value' => 1)),
                    (object)array('key' => 'order_id', 'value' => (object)array('key' => 'lt', 'value' => 5)),
                    (object)array('key' => 'status', 'value' => (object)array('key' => 'eq', 'value' => 2)),
                )
            )
        );

        /** Execute process data with tested method by reference. */
        $this->_helper->wsiArrayUnpacker($rawWsiData);
        $unpackedData = $rawWsiData;

        $this->assertEquals(
            $unpackedData,
            $expectedUnpackedData,
            'Complex filters were formatted incorrectly.'
        );
    }
}
