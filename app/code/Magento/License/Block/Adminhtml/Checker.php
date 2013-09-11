<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_License
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * License Adminhtml Block
 *
 * @category   Magento
 * @package    Magento_License
 */

namespace Magento\License\Block\Adminhtml;

class Checker extends \Magento\Adminhtml\Block\Template
{
    /**
     * Number of days until the expiration of license.
     *
     * @var int
     */
    protected $_daysLeftBeforeExpired;

    /**
     * Сounts the number of days remaining until the expiration of license.
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $data = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getDaysLeftBeforeExpired();
        $this->_daysLeftBeforeExpired = $data['daysLeftBeforeExpired'];
    }

    /**
     * Decides it's time to show warning or not.
     *
     * @return bool
     */
    public function shouldDispalyNotification()
    {
        $magento_license=Mage::helper('Magento\License\Helper\Data');
        if($magento_license->isIoncubeLoaded() && $magento_license->isIoncubeEncoded()) {
            return ($this->_daysLeftBeforeExpired < 31);
        } else {
            return false;
        }
    }


    /**
     * Getter: return counts of days remaining until the expiration of license.
     *
     * @return int
     */
    public function getDaysLeftBeforeExpired()
    {
        return $this->_daysLeftBeforeExpired;
    }

    /**
     * Returns the text to be displayed in the message.
     *
     * @return string
     */
    public function getMessage()
    {
        $message = "";

        $days = $this->getDaysLeftBeforeExpired();

        if($days < 0) {
            $message = __('Act now! Your Magento Enteprise Edition license expired. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif(0 == $days) {
            $message = __('It\'s not too late! Your Magento Enteprise Edition expires today. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.');
        } elseif($days < 31) {
            $message = __('Act soon! Your Magento Enteprise Edition will expire in %1 days. Please contact <a href="mailto:sales@magento.com">sales@magento.com</a> to renew the license.', $days);
        }

        return $message;
    }
}
