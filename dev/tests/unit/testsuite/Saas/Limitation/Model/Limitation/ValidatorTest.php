<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Limitation_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $fixtureTotalCount
     * @param int $fixtureThreshold
     * @param int $inputQuantity
     * @param bool $expectedResult
     * @dataProvider exceedsThresholdDataProvider
     */
    public function testExceedsThreshold($fixtureTotalCount, $fixtureThreshold, $inputQuantity, $expectedResult)
    {
        $limitation = $this->getMock('Saas_Limitation_Model_Limitation_LimitationInterface');
        $limitation->expects($this->any())->method('getThreshold')->will($this->returnValue($fixtureThreshold));
        $limitation->expects($this->any())->method('getTotalCount')->will($this->returnValue($fixtureTotalCount));

        $model = new Saas_Limitation_Model_Limitation_Validator();
        $this->assertEquals($expectedResult, $model->exceedsThreshold($limitation, $inputQuantity));
    }

    /**
     * @return array
     */
    public function exceedsThresholdDataProvider()
    {
        return array(
            'negative threshold'        => array(2, -1, 3, false),
            'zero threshold'            => array(2, 0, 3, false),
            'count + qty < threshold'   => array(2, 6, 3, false),
            'count + qty = threshold'   => array(2, 5, 3, false),
            'count + qty > threshold'   => array(2, 4, 3, true),
        );
    }
}