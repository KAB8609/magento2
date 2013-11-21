<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage currency import services block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Block\Adminhtml\System\Currency\Rate;

class Services extends \Magento\Backend\Block\Template
{
    /**
     * @inherit
     */
    protected $_template = 'system/currency/rate/services.phtml';

    /**
     * @var \Magento\Directory\Model\Currency\Import\Source\ServiceFactory
     */
    protected $_srcCurrencyFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_adminSession;

    /**
     * @param \Magento\Backend\Model\Session $adminSession
     * @param \Magento\Directory\Model\Currency\Import\Source\ServiceFactory $srcCurrencyFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Session $adminSession,
        \Magento\Directory\Model\Currency\Import\Source\ServiceFactory $srcCurrencyFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_adminSession = $adminSession;
        $this->_srcCurrencyFactory = $srcCurrencyFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Create import services form select element
     *
     * @return \Magento\View\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'import_services',
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Html\Select')
                ->setOptions($this->_srcCurrencyFactory->create()->toOptionArray())
                ->setId('rate_services')
                ->setName('rate_services')
                ->setValue($this->_adminSession->getCurrencyRateService(true))
                ->setTitle(__('Import Service'))
        );

        return parent::_prepareLayout();
    }
}
