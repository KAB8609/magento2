<?php
/**
 * Stores event information in Magento database
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event extends Magento_Core_Model_Abstract implements Magento_PubSub_EventInterface
{
    /**
     * Initialize Model
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Resource_Event');
    }

    /**
     * Prepare data to be saved to database
     *
     * @return Magento_Webhook_Model_Event
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->markAsReadyToSend();
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
     * @return Magento_Webhook_Model_Event
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
     * @return Magento_Webhook_Model_Event
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
     * @return Magento_Webhook_Model_Event
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
     * Mark event as ready to send
     *
     * @return Magento_PubSub_EventInterface
     */
    public function markAsReadyToSend()
    {
        $this->setData('status', Magento_PubSub_EventInterface::READY_TO_SEND);
    }

    /**
     * Mark event as processed
     *
     * @return Magento_PubSub_EventInterface
     */
    public function markAsProcessed()
    {
        $this->setData('status', Magento_PubSub_EventInterface::PROCESSED);
    }
}
