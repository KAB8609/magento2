<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscriber grid block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Newsletter\Block\Adminhtml;

class Subscriber extends \Magento\Backend\Block\Template
{
    /**
     * Queue collection
     *
     * @var \Magento\Newsletter\Model\Resource\Queue\Collection
     */
    protected $_queueCollection = null;

    protected $_template = 'subscriber/list.phtml';

    /**
     * @var \Magento\Newsletter\Model\Resource\Queue\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $collectionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepares block to render
     *
     * @return \Magento\Newsletter\Block\Adminhtml\Subscriber
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * Return queue collection with loaded neversent queues
     *
     * @return \Magento\Newsletter\Model\Resource\Queue\Collection
     */
    public function getQueueCollection()
    {
        if (is_null($this->_queueCollection)) {
            /** @var $this->_queueCollection \Magento\Newsletter\Model\Resource\Queue\Collection */
            $this->_queueCollection = $this->_collectionFactory->create()
                ->addTemplateInfo()
                ->addOnlyUnsentFilter()
                ->load();
        }

        return $this->_queueCollection;
    }

    public function getShowQueueAdd()
    {
        return $this->getChildBlock('grid')->getShowQueueAdd();
    }

    /**
     * Return list of neversent queues for select
     *
     * @return array
     */
    public function getQueueAsOptions()
    {
        return $this->getQueueCollection()->toOptionArray();
    }
}
