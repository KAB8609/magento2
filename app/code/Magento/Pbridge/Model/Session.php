<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Zend_Session_SaveHandler_Interface $saveHandler
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Zend_Session_SaveHandler_Interface $saveHandler,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $saveHandler, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
