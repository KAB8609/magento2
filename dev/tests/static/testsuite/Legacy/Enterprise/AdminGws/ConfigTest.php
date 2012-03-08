<?php
/**
 * AdminGWS configuration nodes validator
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_Enterprise_AdminGws_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider Legacy_ConfigTest::configFileDataProvider
     */
    public function testEventSubscriberFormat($file)
    {
        $xml = simplexml_load_file($file);
        $nodes = $xml->xpath(Integrity_Enterprise_AdminGws_ConfigTest::CLASSES_XPATH) ?: array();
        $errors = array();
        /** @var SimpleXMLElement $node */
        foreach ($nodes as $node) {
            if (preg_match('/\_\_/', $node->getName())) {
                $errors[] = $node->getName();
            }
        }
        if ($errors) {
            $this->fail("Obsolete class names detected in {$file}:\n" . implode(PHP_EOL, $errors) . PHP_EOL);
        }
    }
}
