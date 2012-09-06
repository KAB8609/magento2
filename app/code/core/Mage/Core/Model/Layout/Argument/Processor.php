<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument processor
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Processor
{
    /**
     * @var Mage_Core_Model_Layout_Argument_ProcessorConfig
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @var Mage_Core_Model_Layout_Argument_Updater
     */
    protected $_argumentUpdater;

    /**
     * Argument handlers object list
     *
     * @var array
     */
    protected $_argumentHandlers = array();

    /**
     * @param array $args
     * @throws InvalidArgumentException
     */
    public function __construct(array $args = array())
    {
        if (!isset($args['processorConfig']) || !isset($args['objectFactory'])) {
            throw new InvalidArgumentException('Not all required parameters were passed');
        }

        $this->_config = $args['processorConfig'];
        if (false === ($this->_config instanceof Mage_Core_Model_Layout_Argument_ProcessorConfig)) {
            throw new InvalidArgumentException('Passed wrong instance of processor config object');
        }

        $this->_objectFactory = $args['objectFactory'];
        if (false === ($this->_objectFactory instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Passed wrong instance of object factory');
        }
    }

    /**
     * Process given arguments, prepare arguments of custom type.
     * @param array $arguments
     * @throws InvalidArgumentException
     * @return array
     */
    public function process(array $arguments)
    {
        $processedArguments = array();
        foreach ($arguments as $argumentKey => $argumentValue) {
            $value = isset($argumentValue['value']) ? $argumentValue['value'] : null;

            if (true == isset($argumentValue['type']) && false == empty($argumentValue['type'])) {

                if (true == empty($value)) {
                    throw new InvalidArgumentException('Argument value is required for type ' . $argumentValue['type']);
                }

                $handler = $this->_getArgumentHandler($argumentValue['type']);
                $value = $handler->process($value);
            }

            if (true == isset($argumentValue['updater']) && false == empty($argumentValue['updater'])) {
                $value = $this->_getArgumentUpdater()->applyUpdaters($value, $argumentValue['updater']);
            }
            $processedArguments[$argumentKey] = $value;
        }
        return $processedArguments;
    }

    /**
     * Get argument updater instance
     *
     * @return Mage_Core_Model_Layout_Argument_Updater
     */
    protected function _getArgumentUpdater()
    {
        if (null === $this->_argumentUpdater) {
            $this->_argumentUpdater = $this->_objectFactory
                ->getModelInstance('Mage_Core_Model_Layout_Argument_Updater',
                    array('objectFactory' => $this->_objectFactory));
        }
        return $this->_argumentUpdater;
    }

    /**
     * Get argument handler by type
     *
     * @param string $type
     * @throws InvalidArgumentException
     * @return Mage_Core_Model_Layout_Argument_HandlerInterface
     */
    protected function _getArgumentHandler($type)
    {
        if (isset($this->_argumentHandlers[$type])) {
            return $this->_argumentHandlers[$type];
        }

        $handlerFactory = $this->_config->getArgumentHandlerFactoryByType($type);

        if (false === ($handlerFactory instanceof Mage_Core_Model_Layout_Argument_HandlerFactoryInterface)) {
            throw new InvalidArgumentException('Incorrect handler factory');
        }

        /** @var $handler Mage_Core_Model_Layout_Argument_HandlerInterface */
        $handler = $handlerFactory->createHandler();

        if (false === ($handler instanceof Mage_Core_Model_Layout_Argument_HandlerInterface)) {
            throw new InvalidArgumentException($type
                . ' type handler should implement Mage_Core_Model_Layout_Argument_HandlerInterface');
        }

        $this->_argumentHandlers[$type] = $handler;
        return $handler;
    }
}
