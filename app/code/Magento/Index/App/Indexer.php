<?php
/**
 * Indexer application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App;

use Magento\AppInterface,
    Magento\Filesystem;

class Indexer implements AppInterface
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento\Index\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @param string $reportDir
     * @param Filesystem $filesystem
     * @param \Magento\Index\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        $reportDir,
        Filesystem $filesystem,
        \Magento\Index\Model\IndexerFactory $indexerFactory
    ) {
        $this->_reportDir = $reportDir;
        $this->_filesystem = $filesystem;
        $this->_indexerFactory = $indexerFactory;
    }

    /**
     * Run application
     *
     * @return int
     */
    public function execute()
    {
        /* Clean reports */
        $this->_filesystem->delete($this->_reportDir, dirname($this->_reportDir));

        /* Run all indexer processes */
        /** @var $indexer \Magento\Index\Model\Indexer */
        $indexer = $this->_indexerFactory->create();
        /** @var $process \Magento\Index\Model\Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
        return 0;
    }
}
