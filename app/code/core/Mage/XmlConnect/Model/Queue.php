<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Model_Queue extends Mage_Core_Model_Template
{
    const STATUS_IN_QUEUE   = 0;
    const STATUS_CANCELED   = 1;
    const STATUS_COMPLETED  = 2;
    const STATUS_DELETED    = 3;

    const MESSAGE_TYPE_AIRMAIL  = 'airmail';
    const MESSAGE_TYPE_PUSH     = 'push';

    const XML_PATH_NOTIFICATION_TYPE = 'xmlconnect/devices/%s/notification_type';
    const XML_PATH_CRON_MESSAGES_COUNT = 'xmlconnect/mobile_application/cron_send_messages_count';

    protected $_appType = null;

    /**
     * Initialize queue message
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/queue');
    }

    /**
     * Load object data
     *
     * @param   integer $id
     * @return  Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        parent::load($id, $field);

        if ($this->getTemplateId()) {
            $this->setName(
                Mage::getModel('xmlconnect/template')->load($this->getTemplateId())->getName()
            );
        }
        return $this;
    }

    /**
     * Get template type
     *
     * @return int
     */
    public function getType()
    {
        return self::TYPE_HTML;
    }

    /**
     * Getter for application type
     * @return string
     */
    public function getApplicationType()
    {
        if (empty($this->_appType) && $this->getAppCode()) {
            $app = Mage::getModel('xmlconnect/application')->loadByCode($this->getAppCode());
            $this->_appType = $app->getId() ? $app->getType() : null;
        }

        return $this->_appType;
    }

    /**
     * Getter for application name
     * @return string
     */
    public function getAppName()
    {
        return $this->getApplicationName() ? $this->getApplicationName() : Mage::helper('xmlconnect')->getApplicationName($this->getAppCode());
    }

    /**
     * Getter for template name
     * @return string
     */
    public function getTplName()
    {
        return $this->getTemplateName() ? $this->getTemplateName() : Mage::helper('xmlconnect')->getTemplateName($this->getTemplateId());
    }

    /**
     * Retrieve processed template
     *
     * @param array $variables
     * @param bool $usePreprocess
     * @return string
     */
    public function getProcessedTemplate(array $variables = array(), $usePreprocess = false)
    {
        /* @var $processor Mage_Widget_Model_Template_Filter */
        $processor = Mage::getModel('widget/template_filter');

        $variables['this'] = $this;

        if (Mage::app()->isSingleStoreMode()) {
            $processor->setStoreId(Mage::app()->getStore());
        } else {
            $processor->setStoreId(1);
        }

        $htmlDescription = '<div style="font-size: 0.8em; text-decoration: underline; margin-top: 1.5em; line-height: 2em;">%s:</div>';

        switch ($this->getData('type')) {
            case Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_AIRMAIL:
                $html  = sprintf($htmlDescription, Mage::helper('xmlconnect')->__('Push title')) . $this->getPushTitle();
                $html .= sprintf($htmlDescription, Mage::helper('xmlconnect')->__('Message title')) . $this->getMessageTitle();
                $html .= sprintf($htmlDescription, Mage::helper('xmlconnect')->__('Message content')) . $processor->filter($this->getContent());
                break;
            case Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_PUSH:
            default:
                $html  = sprintf($htmlDescription, Mage::helper('xmlconnect')->__('Push title')) . $this->getPushTitle();
                break;
        }
        return $html;
    }

    /**
     * Reset all model data
     *
     * @return Mage_XmlConnect_Model_Queue
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();

        return $this;
    }


    /**
     * Get JSON-encoded params for broadcast AirMail
     *  Format of JSON data:
     *  {
     *      "push": {
     *          "aps": {
     *              "alert": "New message!"
     *          }
     *      },
     *      "title": "Message title",
     *      "message": "Your full message here.",
     *      "extra": {
     *          "some_key": "some_value"
     *      }
     *  }
     *
     * @return string
     */
    public function getAirmailBroadcastParams()
    {
        $notificationType = Mage::getStoreConfig(sprintf(Mage_XmlConnect_Model_Queue::XML_PATH_NOTIFICATION_TYPE, $this->getApplicationType()));

        $payload = array(
            'push' => array(
                $notificationType => array(
                    'alert' => $this->getPushTitle(),
                )
            ),
            'title' => $this->getMessageTitle(),
            'message' => $this->getContent(),
        );
        return Mage::helper('core')->jsonEncode($payload);
    }

    /**
     * Get JSON-encoded params for broadcast Push Notification
     *  Format of JSON data:
     *  {
     *      "aps": {
     *           "badge": 15,
     *           "alert": "Hello from Urban Airship!",
     *           "sound": "cat.caf"
     *      },
     *      "exclude_tokens": [
     *          "device token you want to skip",
     *          "another device token you want to skip"
     *      ]
     *  }
     *
     * @return string
     */
    public function getPushBroadcastParams()
    {
        $notificationType = Mage::getStoreConfig(sprintf(Mage_XmlConnect_Model_Queue::XML_PATH_NOTIFICATION_TYPE, $this->getApplicationType()));

        $payload = array(
            $notificationType => array(
//                'badge' => 'auto',
                'alert' => $this->getPushTitle(),
                'sound' => 'default'
            )
        );
        return Mage::helper('core')->jsonEncode($payload);
    }

    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        if (!$this->getIsSent() && $this->getStatus() == self::STATUS_IN_QUEUE) {
            try {
                Mage::dispatchEvent('before_save_message_queue', array('queueMessage' => $this));
            }
            catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return parent::save();
    }

}
