<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Convert;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Convert\Xml
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Convert\Xml;
    }

    public function testXmlToAssoc()
    {
        $xmlstr = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<_><one>1</one><two><three>3</three><four>4</four></two></_>
XML;
        $result = $this->_model->xmlToAssoc(new \SimpleXMLElement($xmlstr));
        $this->assertEquals(array('one' => '1', 'two' => array('three' => '3', 'four'  => '4')), $result);
    }
}