<?php
/**
 * Captcha model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Captcha_Model_CaptchaFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get captcha instance
     *
     * @param string $instanceName
     * @param array $params
     * @return Mage_Captcha_Model_Interface
     * @throws InvalidArgumentException
     */
    public function create($instanceName, array $params = array())
    {
        $instance = $this->_objectManager->create($instanceName, $params);
        if (!($instance instanceof Mage_Captcha_Model_Interface)) {
            throw new InvalidArgumentException($instanceName . ' does not implements Mage_Captcha_Model_Interface');
        }
        return $instance;
    }
}