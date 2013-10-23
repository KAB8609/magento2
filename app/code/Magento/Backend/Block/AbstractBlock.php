<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend abstract block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 */
namespace Magento\Backend\Block;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractBlock extends \Magento\Core\Block\AbstractBlock
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
        $this->_logger = $context->getLogger();
    }
}
