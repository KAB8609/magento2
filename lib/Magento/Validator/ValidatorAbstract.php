<?php
/**
 * Abstract validator class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Validator;

abstract class ValidatorAbstract implements \Magento\Validator\ValidatorInterface
{
    /**
     * @var \Magento\Translate\AdapterInterface|null
     */
    protected static $_defaultTranslator = null;

    /**
     * @var \Magento\Translate\AdapterInterface|null
     */
    protected $_translator = null;

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Set default translator instance
     *
     * @param \Magento\Translate\AdapterInterface|null $translator
     */
    public static function setDefaultTranslator(\Magento\Translate\AdapterInterface $translator = null)
    {
        self::$_defaultTranslator = $translator;
    }

    /**
     * Get default translator
     *
     * @return \Magento\Translate\AdapterInterface|null
     */
    public static function getDefaultTranslator()
    {
        return self::$_defaultTranslator;
    }

    /**
     * Set translator instance
     *
     * @param \Magento\Translate\AdapterInterface|null $translator
     * @return \Magento\Validator\ValidatorAbstract
     */
    public function setTranslator($translator = null)
    {
        $this->_translator = $translator;
        return $this;
    }

    /**
     * Get translator
     *
     * @return \Magento\Translate\AdapterInterface|null
     */
    public function getTranslator()
    {
        if (is_null($this->_translator)) {
            return self::getDefaultTranslator();
        }
        return $this->_translator;
    }

    /**
     * Check that translator is set.
     *
     * @return boolean
     */
    public function hasTranslator()
    {
        return !is_null($this->_translator);
    }

    /**
     * Get validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages
     */
    protected function _clearMessages()
    {
        $this->_messages = array();
    }

    /**
     * Add messages
     *
     * @param array $messages
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }
}
