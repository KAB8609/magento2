<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth nonce resource collection model
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Integration\Model\Resource\Oauth\Nonce;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Integration\Model\Oauth\Nonce', 'Magento\Integration\Model\Resource\Oauth\Nonce');
    }
}