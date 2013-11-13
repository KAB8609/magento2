<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block;

class Agreements extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Checkout\Model\Resource\Agreement\CollectionFactory
     */
    protected $_agreementCollFactory;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Checkout\Model\Resource\Agreement\CollectionFactory $agreementCollFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Checkout\Model\Resource\Agreement\CollectionFactory $agreementCollFactory,
        array $data = array()
    ) {
        $this->_agreementCollFactory = $agreementCollFactory;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * @return mixed
     */
    public function getAgreements()
    {
        if (!$this->hasAgreements()) {
            if (!$this->_storeConfig->getConfigFlag('checkout/options/enable_agreements')) {
                $agreements = array();
            } else {
                /** @var \Magento\Checkout\Model\Resource\Agreement\Collection $agreements */
                $agreements = $this->_agreementCollFactory->create()
                    ->addStoreFilter($this->_storeManager->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
            $this->setAgreements($agreements);
        }
        return $this->getData('agreements');
    }
}
