<?php
/**
 * Stores event information in Magento database
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Mage_Webhook_Model_Event setStatus()
 * @method Mage_Webhook_Model_Event setUpdatedAt()
 * @method Mage_Webhook_Model_Event setCreatedAt()
 */
class Mage_Webhook_Model_Event extends Mage_Core_Model_Abstract implements Magento_PubSub_EventInterface
{
    /**
     * Initialize Model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Webhook_Model_Resource_Event');
        $this->setStatus(Magento_PubSub_EventInterface::STATUS_READY_TO_SEND);
    }

    /**
     * Prepare data to be saved to database
     *
     * @return Mage_Webhook_Model_Event
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->_getResource()->formatDate(true));
        } elseif ($this->getId() && !$this->hasData('updated_at')) {
            $this->setUpdatedAt($this->_getResource()->formatDate(true));
        }
        return $this;
    }

    /**
     * Prepare data before set
     *
     * @param array $data
     * @return Mage_Webhook_Model_Event
     */
    public function setBodyData(array $data)
    {
        return $this->setData('body_data', serialize($data));
    }

    /**
     * Prepare data before return
     *
     * @return array
     */
    public function getBodyData()
    {
        $data = $this->getData('body_data');
        if (!is_null($data)) {
            return unserialize($data);
        }
        return array();
    }

    /**
     * Prepare headers before set
     *
     * @param array $headers
     * @return Mage_Webhook_Model_Event
     */
    public function setHeaders(array $headers)
    {
        return $this->setData('headers', serialize($headers));
    }

    /**
     * Prepare headers before return
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = $this->getData('headers');
        if (!is_null($headers)) {
            return unserialize($headers);
        }
        return array();
    }

    /**
     * Prepare options before set
     *
     * @param array $options
     * @return Mage_Webhook_Model_Event
     */
    public function setOptions(array $options)
    {
        return $this->setData('options', serialize($options));
    }

    /**
     * Return status. Enable compatibility with interface
     *
     * @return null|int
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * Return topic and enable compatibility with interface
     *
     * @return null|string
     */
    public function getTopic()
    {
        return $this->getData('topic');
    }

    /**
     * Mark event as processed
     *
     * @return Mage_Webhook_Model_Event
     */
    public function complete()
    {
        $this->setData('status', Magento_PubSub_EventInterface::STATUS_PROCESSED)
            ->save();
        return $this;
    }

    /**
     * Mark event as processed
     *
     * @return Mage_Webhook_Model_Event
     */
    public function markAsInProgress()
    {
        $this->setData('status', Magento_PubSub_EventInterface::STATUS_IN_PROGRESS);
        return $this;
    }
}
