<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Install_Block_Config extends Magento_Install_Block_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'config.phtml';

    /**
     * Retrieve form data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/configPost');
    }

    /**
     * Retrieve configuration form data object
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('Magento_Install_Model_Session')->getConfigData(true);
            if (empty($data)) {
                $data = Mage::getModel('Magento_Install_Model_Installer_Config')->getFormData();
            } else {
                $data = new Magento_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function getSkipUrlValidation()
    {
        return Mage::getSingleton('Magento_Install_Model_Session')->getSkipUrlValidation();
    }

    /**
     * @return bool
     */
    public function getSkipBaseUrlValidation()
    {
        return Mage::getSingleton('Magento_Install_Model_Session')->getSkipBaseUrlValidation();
    }

    /**
     * @return array
     */
    public function getSessionSaveOptions()
    {
        return array(
            'files' => Mage::helper('Magento_Install_Helper_Data')->__('File System'),
            'db'    => Mage::helper('Magento_Install_Helper_Data')->__('Database'),
        );
    }

    /**
     * @return string
     */
    public function getSessionSaveSelect()
    {
        $html = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('config[session_save]')
            ->setId('session_save')
            ->setTitle(Mage::helper('Magento_Install_Helper_Data')->__('Save Session Files In'))
            ->setClass('required-entry')
            ->setOptions($this->getSessionSaveOptions())
            ->getHtml();
        return $html;
    }
}
