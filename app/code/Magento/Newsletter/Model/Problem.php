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
 * Newsletter problem model
 *
 * @method \Magento\Newsletter\Model\Resource\Problem _getResource()
 * @method \Magento\Newsletter\Model\Resource\Problem getResource()
 * @method int getSubscriberId()
 * @method \Magento\Newsletter\Model\Problem setSubscriberId(int $value)
 * @method int getQueueId()
 * @method \Magento\Newsletter\Model\Problem setQueueId(int $value)
 * @method int getProblemErrorCode()
 * @method \Magento\Newsletter\Model\Problem setProblemErrorCode(int $value)
 * @method string getProblemErrorText()
 * @method \Magento\Newsletter\Model\Problem setProblemErrorText(string $value)
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model;

class Problem extends \Magento\Core\Model\AbstractModel
{
    /**
     * Current Subscriber
     * 
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected  $_subscriber = null;

    /**
     * Initialize Newsletter Problem Model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Newsletter\Model\Resource\Problem');
    }

    /**
     * Add Subscriber Data
     *
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return \Magento\Newsletter\Model\Problem
     */
    public function addSubscriberData(\Magento\Newsletter\Model\Subscriber $subscriber)
    {
        $this->setSubscriberId($subscriber->getId());
        return $this;
    }

    /**
     * Add Queue Data
     *
     * @param \Magento\Newsletter\Model\Queue $queue
     * @return \Magento\Newsletter\Model\Problem
     */
    public function addQueueData(\Magento\Newsletter\Model\Queue $queue)
    {
        $this->setQueueId($queue->getId());
        return $this;
    }

    /**
     * Add Error Data
     *
     * @param \Exception $e
     * @return \Magento\Newsletter\Model\Problem
     */
    public function addErrorData(\Exception $e)
    {
        $this->setProblemErrorCode($e->getCode());
        $this->setProblemErrorText($e->getMessage());
        return $this;
    }

    /**
     * Retrieve Subscriber
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriber()
    {
        if(!$this->getSubscriberId()) {
            return null;
        }

        if(is_null($this->_subscriber)) {
            $this->_subscriber = \Mage::getModel('\Magento\Newsletter\Model\Subscriber')
                ->load($this->getSubscriberId());
        }

        return $this->_subscriber;
    }

    /**
     * Unsubscribe Subscriber
     *
     * @return \Magento\Newsletter\Model\Problem
     */
    public function unsubscribe()
    {
        if($this->getSubscriber()) {
            $this->getSubscriber()->setSubscriberStatus(\Magento\Newsletter\Model\Subscriber::STATUS_UNSUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
        }
        return $this;
    }

}
