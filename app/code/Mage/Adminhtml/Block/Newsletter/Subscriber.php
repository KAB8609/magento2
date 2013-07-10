<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscriber grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber extends Mage_Adminhtml_Block_Template
{
    /**
     * Queue collection
     *
     * @var Mage_Newsletter_Model_Resource_Queue_Collection
     */
    protected $_queueCollection = null;

    protected $_template = 'newsletter/subscriber/list.phtml';

    /**
     * Prepares block to render
     *
     * @return Mage_Adminhtml_Block_Newsletter_Subscriber
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * Return queue collection with loaded neversent queues
     *
     * @return Mage_Newsletter_Model_Resource_Queue_Collection
     */
    public function getQueueCollection()
    {
        if (is_null($this->_queueCollection)) {
            /** @var $this->_queueCollection Mage_Newsletter_Model_Resource_Queue_Collection */
            $this->_queueCollection = Mage::getResourceSingleton('Mage_Newsletter_Model_Resource_Queue_Collection')
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
