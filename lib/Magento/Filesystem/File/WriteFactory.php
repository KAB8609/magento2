<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filesystem\File;

class WriteFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create a readable file
     *
     * @param string $path
     * @param string $mode
     * @return \Magento\Filesystem\File\WriteInterface
     */
    public function create($path, $mode)
    {
        return $this->objectManager->create('Magento\Filesystem\File\Write', array('path' => $path, 'mode' => $mode));
    }
}