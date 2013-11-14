<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml sales order create newsletter block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Load extends \Magento\View\Block\Template
{
    /**
     * Adminhtml js
     *
     * @var \Magento\Adminhtml\Helper\Js
     */
    protected $_adminhtmlJs = null;

    /**
     * @param \Magento\Adminhtml\Helper\Js $adminhtmlJs
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\View\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Helper\Js $adminhtmlJs,
        \Magento\Core\Helper\Data $coreData,
        \Magento\View\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlJs = $adminhtmlJs;
        parent::__construct($coreData, $context, $data);
    }

    protected function _toHtml()
    {
        $result = array();
        $layout = $this->getLayout();
        foreach ($this->getChildNames() as $name) {
            $result[$name] = $layout->renderElement($name);
        }
        $resultJson = $this->_coreData->jsonEncode($result);
        $jsVarname = $this->getRequest()->getParam('as_js_varname');
        if ($jsVarname) {
            return $this->_adminhtmlJs->getScript(sprintf('var %s = %s', $jsVarname, $resultJson));
        } else {
            return $resultJson;
        }
    }
}
