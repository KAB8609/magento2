<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for File
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomAttribute\Block\Form\Renderer;

use Magento\View\Element\AbstractBlock;
use Magento\View\Element\Template;

class File extends \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        Template\Context $context,
        array $data = array()
    ) {
        $this->_coreData =$coreData;
        parent::__construct($context, $data);
    }

    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getValue()) {
            return $this->escapeHtml($this->_coreData->urlEncode($this->getValue()));
        }
        return '';
    }
}
