<?php
/**
 * Default session storage
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Sesstion
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;

class Storage extends \Magento\Object implements StorageInterface
{
    /**
     * Namespace of storage
     *
     * @var string
     */
    protected $namespace;

    /**
     * @param string $namespace
     * @param array $data
     */
    public function __construct($namespace = 'default', array $data = array())
    {
        $this->namespace = $namespace;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function init(array $data)
    {
        $namespace = $this->getNamespace();
        if (isset($data[$namespace])) {
            $this->setData($data[$namespace]);
        }
        $_SESSION[$namespace] = &$this->_data;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
