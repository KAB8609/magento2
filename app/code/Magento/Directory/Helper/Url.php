<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * \Directory URL helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Helper;

class Url extends \Magento\Core\Helper\Url
{
    /**
     * Retrieve switch currency url
     *
     * @param array $params Additional url params
     * @return string
     */
    public function getSwitchCurrencyUrl($params = array())
    {
        $params = is_array($params) ? $params : array();

        if ($this->_getRequest()->getAlias('rewrite_request_path')) {
            $url = \Mage::app()->getStore()->getBaseUrl() . $this->_getRequest()->getAlias('rewrite_request_path');
        }
        else {
            $url = $this->getCurrentUrl();
        }
        $params[\Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED] = \Mage::helper('Magento\Core\Helper\Data')
            ->urlEncode($url);

        return $this->_getUrl('directory/currency/switch', $params);
    }

    public function getLoadRegionsUrl()
    {

    }
}
