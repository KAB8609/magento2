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
 * OAuth authorization block with auth buttons
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Block\Authorize;

class Button extends \Magento\Oauth\Block\Authorize\ButtonBaseAbstract
{
    /**
     * Retrieve confirm authorization url path
     *
     * @return string
     */
    public function getConfirmUrlPath()
    {
        return 'oauth/authorize/confirm';
    }

    /**
     * Retrieve reject authorization url path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'oauth/authorize/reject';
    }
}
