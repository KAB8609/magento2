<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model;

class Encryption extends \Magento\Pci\Model\Encryption
{

    /**
     * Constructor
     *
     * @param \Magento\Math\Random $randomGenerator
     * @param \Magento\Encryption\CryptFactory $cryptFactory
     * @param string $cryptKey
     * @param $key
     */
    public function __construct(
        \Magento\Math\Random $randomGenerator,
        \Magento\Encryption\CryptFactory $cryptFactory,
        $cryptKey,
        $key
    ) {
        parent::__construct($randomGenerator, $cryptFactory, $cryptKey);
        $this->_keys = array($key);
        $this->_keyVersion = 0;
    }

    /**
     * Look for key and crypt versions in encrypted data before decrypting
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return parent::decrypt($this->_keyVersion . ':' . self::CIPHER_LATEST . ':' . $data);
    }

    /**
     * Prepend IV to encrypted data after encrypting
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $crypt = $this->_getCrypt();
        return $crypt->getInitVector() . ':' . base64_encode($crypt->encrypt((string)$data));
    }
}
