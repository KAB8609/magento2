<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Di\Code\Scanner;

class XmlScanner implements ScannerInterface
{
    /**
     * @var \Magento\Tools\Di\Compiler\Log\Log $log
     */
    protected $_log;

    /**
     * @param \Magento\Tools\Di\Compiler\Log\Log $log
     */
    public function __construct(\Magento\Tools\Di\Compiler\Log\Log $log)
    {
        $this->_log = $log;
    }

    /**
     * Get array of class names
     *
     * @param array $files
     * @return array
     */
    public function collectEntities(array $files)
    {
        $output = array();
        foreach ($files as $file) {
            $dom = new \DOMDocument();
            $dom->load($file);
            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace("php", "http://php.net/xpath");
            $xpath->registerPhpFunctions('preg_match');
            $regex = '/(.*)Proxy/';
            $query = "/config/preference[ php:functionString('preg_match', '$regex', @type) > 0] | "
                . "/config/type/param/instance[ php:functionString('preg_match', '$regex', @type) > 0] | "
                . "/config/virtualType[ php:functionString('preg_match', '$regex', @type) > 0]";
            /** @var \DOMNode $node */
            foreach ($xpath->query($query) as $node) {
                $output[] = $node->attributes->getNamedItem('type')->nodeValue;
            }
        }
        $output = array_unique($output);
        return $this->_filterEntities($output);
    }

    /**
     * Filter found entities if needed
     *
     * @param array $output
     * @return array
     */
    protected function _filterEntities(array $output)
    {
        $filteredEntities = array();
        foreach ($output as $className) {
            $entityName = substr($className, -6) === '\Proxy' ? substr($className, 0, -6) : $className;
            if (false === class_exists($className)) {
                if (class_exists($entityName)) {
                    array_push($filteredEntities, $className);
                } else {
                    $this->_log->add(
                        \Magento\Tools\Di\Compiler\Log\Log::CONFIGURATION_ERROR,
                        $className,
                        'Invalid proxy class for ' . substr($className, 0, -5)
                    );
                }
            }
        }
        return $filteredEntities;
    }
}