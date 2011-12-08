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
 * Download Magento core modules and updates choice (online, offline)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Download extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('download.phtml');
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/downloadPost');
    }

    public function getNextUrl()
    {
        return Mage::getModel('Mage_Install_Model_Wizard')
            ->getStepByName('download')
                ->getNextUrl();
    }

    public function hasLocalCopy()
    {
        $dir = Mage::getConfig()->getModuleDir('etc', 'Mage_Adminhtml');
        if ($dir && file_exists($dir)) {
            return true;
        }
        return false;
    }
}

