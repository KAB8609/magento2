<?php
/**
 * Module declaration file resolver. Reads list of module declaration files from module /etc directories.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Module\Declaration;

class FileResolver implements \Magento\Config\FileResolverInterface
{
    /**
     * @var \Magento\App\Dir
     */
    protected $_applicationDirs;

    /**
     * @param \Magento\App\Dir $applicationDirs
     */
    public function __construct(\Magento\App\Dir $applicationDirs)
    {
        $this->_applicationDirs = $applicationDirs;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($filename, $scope)
    {
        $appCodeDir =  $this->_applicationDirs->getDir(\Magento\App\Dir::MODULES);
        $moduleFilePattern = $appCodeDir . '/*/*/etc/module.xml';
        $moduleFileList = glob($moduleFilePattern);

        $mageScopePath = $appCodeDir . '/Magento/';
        $output = array(
            'base' => array(),
            'mage' => array(),
            'custom' => array(),
        );
        foreach ($moduleFileList as $file) {
            $scope = strpos($file, $mageScopePath) === 0 ? 'mage' : 'custom';
            $output[$scope][] = $file;
        }

        $appConfigDir = $this->_applicationDirs->getDir(\Magento\App\Dir::CONFIG);
        $globalEnablerPattern = $appConfigDir . '/*/module.xml';
        $output['base'] = glob($globalEnablerPattern);
        // Put global enablers at the end of the file list
        return array_merge($output['mage'], $output['custom'], $output['base']);
    }

}
