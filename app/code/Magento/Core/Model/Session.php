<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core session model
 *
 * @todo extend from \Magento\Core\Model\Session\AbstractSession
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method \Magento\Core\Model\Session setCookieShouldBeReceived(bool $flag)
 * @method \Magento\Core\Model\Session unsCookieShouldBeReceived()
 */
namespace Magento\Core\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param \Magento\Math\Random $mathRandom
     * @param array $data
     * @param string|null $sessionName
     * @internal param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        \Magento\Math\Random $mathRandom,
        array $data = array(),
        $sessionName = null
    ) {
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $saveHandler, $data);
        $this->init('core', $sessionName);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', $this->mathRandom->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
