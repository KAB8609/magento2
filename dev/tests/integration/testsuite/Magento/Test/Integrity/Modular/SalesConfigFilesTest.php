<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

class SalesConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * attributes represent merging rules
     * copied from original namespace Magento\App\Route\Config;
     *
     * class Reader
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/section' => 'name',
        '/config/section/group' => 'name',
        '/config/section/group/item' => 'name',
        '/config/section/group/item/renderer' => 'name',
        '/config/order/available_product_type' => 'name'
    );

    /**
     * Path to tough XSD for merged file validation
     *
     * @var string
     */
    protected $_mergedSchemaFile;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_mergedSchemaFile = $objectManager->get('Magento\Sales\Model\Config\SchemaLocator')->getSchema();
    }

    public function testSalesConfigFiles()
    {
        $invalidFiles = array();

        $files = \Magento\TestFramework\Utility\Files::init()->getConfigFiles('sales.xml');
        $mergedConfig = new \Magento\Config\Dom(
            '<config></config>',
            $this->_idAttributes
        );

        foreach ($files as $file) {
            $content = file_get_contents($file[0]);
            try {
                new \Magento\Config\Dom($content, $this->_idAttributes);
                //merge won't be performed if file is invalid because of exception thrown
                $mergedConfig->merge($content);
            } catch (\Magento\Config\Dom\ValidationException $e) {
                $invalidFiles[] = $file[0];
            }
        }

        if (!empty($invalidFiles)) {
            $this->fail('Found broken files: ' . implode("\n", $invalidFiles));
        }

        $errors = array();
        $mergedConfig->validate($this->_mergedSchemaFile, $errors);
        if ($errors) {
            $this->fail('Merged routes config is invalid: ' . "\n" . implode("\n", $errors));
        }
    }
}