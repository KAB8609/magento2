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
 * Adminhtml order create gift message block
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Giftmessage extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Magento\GiftMessage\Model\Save
     */
    protected $_giftMessageSave;

    /**
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftMessageSave = $giftMessageSave;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    /**
     * Generate form for editing of gift message for entity
     *
     * @param \Magento\Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(\Magento\Object $entity, $entityType='quote') {
        return $this->getLayout()
            ->createBlock('Magento\Sales\Block\Adminhtml\Order\Create\Giftmessage\Form')
            ->setEntity($entity)
            ->setEntityType($entityType)
            ->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
               && $this->helper('Magento\GiftMessage\Helper\Message')->getIsMessagesAvailable('item',
                        $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
            return $items;
        }

        return false;
    }

    /**
     * Retrieve gift message save model
     *
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_giftMessageSave;
    }

}
