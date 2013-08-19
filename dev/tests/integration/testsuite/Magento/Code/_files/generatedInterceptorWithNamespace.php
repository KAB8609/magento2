<?php
namespace Magento\Code\Generator\TestAsset;
/**
 * Interceptor class for Magento\Code\Generator\TestAsset\SourceClassWithNamespace
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class SourceClassWithNamespaceInterceptor extends \Magento\Code\Generator\TestAsset\SourceClassWithNamespace
{
    /**
     * Object Manager factory
     *
     * @var \Magento_ObjectManager_Factory
     */
    protected $_factory = null;

    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Subject type
     *
     * @var string
     */
    protected $_subjectType = null;

    /**
     * Subject
     *
     * @var \Magento\Code\Generator\TestAsset\SourceClassWithNamespace
     */
    protected $_subject = null;

    /**
     * List of plugins
     *
     * @var \Magento_Interception_PluginList
     */
    protected $_pluginList = null;

    /**
     * Subject constructor arguments
     *
     * @var array
     */
    protected $_arguments = null;

    /**
     * Interceptor constructor
     *
     * @param \Magento_ObjectManager_Factory $factory
     * @param \Magento_ObjectManager_ObjectManager $objectManager
     * @param string $subjectType
     * @param \Magento_Interception_PluginList $pluginList
     * @param array $arguments
     */
    public function __construct(\Magento_ObjectManager_Factory $factory, \Magento_ObjectManager_ObjectManager $objectManager, $subjectType, \Magento_Interception_PluginList $pluginList, array $arguments)
    {
        $this->_factory = $factory;
        $this->_objectManager = $objectManager;
        $this->_subjectType = $subjectType;
        $this->_pluginList = $pluginList;
        $this->_arguments = $arguments;
    }

    /**
     * Retrieve subject
     *
     * @return mixed
     */
    protected function _getSubject()
    {
        if (is_null($this->_subject)) {
            $this->_subject = $this->_factory->create($this->_subjectType, $this->_arguments);
        }
        return $this->_subject;
    }

    /**
     * Invoke method
     *
     * @param string $methodName
     * @param array $methodArguments
     * @return mixed
     */
    protected function _invoke($methodName, array $methodArguments)
    {
        $beforeMethodName = 'before' . $methodName;
        foreach ($this->_pluginList->getPlugins($this->_subjectType, $methodName, 'before') as $plugin) {
            $methodArguments = $this->_objectManager->get($plugin)
                ->$beforeMethodName($methodArguments);
        }
        $invocationChain = new \Magento_Code_Plugin_InvocationChain(
            $this->_getSubject(),
            $methodName,
            $this->_objectManager,
            $this->_pluginList->getPlugins($this->_subjectType, $methodName, 'around')
        );
        $invocationResult = $invocationChain->proceed($methodArguments);
        $afterMethodName = 'after' . $methodName;
        $afterPlugins = array_reverse(
            $this->_pluginList->getPlugins($this->_subjectType, $methodName, 'after')
        );
        foreach ($afterPlugins as $plugin) {
            $invocationResult = $this->_objectManager->get($plugin)
                ->$afterMethodName($invocationResult);
        }
        return $invocationResult;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        $this->_getSubject();
        return array('_subject');
    }

    /**
     * Clone subject instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Retrieve ObjectManager from the global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_pluginList = $this->_objectManager->get('Magento_Interception_PluginList');
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildMethod(\Zend\Code\Generator\ClassGenerator $classGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        return $this->_invoke('publicChildMethod', func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function publicMethodWithReference(\Zend\Code\Generator\ClassGenerator &$classGenerator, &$param1, array &$array)
    {
        return $this->_invoke('publicMethodWithReference', func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function publicChildWithoutParameters()
    {
        return $this->_invoke('publicChildWithoutParameters', func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentMethod(\Zend\Code\Generator\DocBlockGenerator $docBlockGenerator, $param1 = '', $param2 = '\\', $param3 = '\'', array $array = array())
    {
        return $this->_invoke('publicParentMethod', func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function publicParentWithoutParameters()
    {
        return $this->_invoke('publicParentWithoutParameters', func_get_args());
    }
}
