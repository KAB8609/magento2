<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation ending block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_End extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('end.phtml');
    }

    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = (string) Mage::getConfig()->getNode('global/crypt/key');
            $this->setData('encryption_key', $key);
        }
        return $key;
    }

    /**
     * Return url for iframe source
     *
     * @return string
     */
    public function getIframeSourceUrl()
    {
        if (!Mage_AdminNotification_Model_Survey::isSurveyUrlValid()
            || Mage::getSingleton('Mage_Install_Model_Installer')->getHideIframe()) {
            return null;
        }
        return Mage_AdminNotification_Model_Survey::getSurveyUrl();
    }
}
