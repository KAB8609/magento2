<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Magento info API
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Magento;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Retrieve information about current Magento installation
     *
     * @return array
     */
    public function info()
    {
        $result = array();
        $result['magento_edition'] = \Mage::getEdition();
        $result['magento_version'] = \Mage::getVersion();

        return $result;
    }
}
