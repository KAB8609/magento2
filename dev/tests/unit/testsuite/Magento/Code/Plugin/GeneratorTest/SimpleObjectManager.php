<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code\Plugin\GeneratorTest;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SimpleObjectManager implements \Magento\ObjectManager
{

    /**
     * Create new object instance
     *
     * @param string $type
     * @param array $arguments
     * @return mixed
     */
    public function create($type, array $arguments = array())
    {
        return new $type($arguments);
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    public function get($type)
    {
        return $this->create($type);
    }

    /**
     * Configure object manager
     *
     * @param array $configuration
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configure(array $configuration)
    {
    }
}
