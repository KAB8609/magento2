<?php
/**
 * Nonce builder factory.
 *
 * @copyright {copyright}
 */
namespace Magento\Integration\Model\Oauth\Nonce;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create nonce model.
     *
     * @param array $arguments
     * @return \Magento\Integration\Model\Oauth\Nonce
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Magento\Integration\Model\Oauth\Nonce', $arguments);
    }
}
