<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Visitor log resource
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Resource_Visitor extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('log_visitor', 'visitor_id');
    }

    /**
     * Prepare data for save
     *
     * @param Magento_Core_Model_Abstract $visitor
     * @return array
     */
    protected function _prepareDataForSave(Magento_Core_Model_Abstract $visitor)
    {
        return array(
            'session_id'        => $visitor->getSessionId(),
            'first_visit_at'    => $visitor->getFirstVisitAt(),
            'last_visit_at'     => $visitor->getLastVisitAt(),
            'last_url_id'       => $visitor->getLastUrlId() ? $visitor->getLastUrlId() : 0,
            'store_id'          => Mage::app()->getStore()->getId(),
        );
    }

    /**
     * Saving information about url
     *
     * @param   Magento_Log_Model_Visitor $visitor
     * @return  Magento_Log_Model_Resource_Visitor
     */
    protected function _saveUrlInfo($visitor)
    {
        $adapter    = $this->_getWriteAdapter();
        $data       = new \Magento\Object(array(
            'url'    => Mage::helper('Magento_Core_Helper_String')->substr($visitor->getUrl(), 0, 250),
            'referer'=> Mage::helper('Magento_Core_Helper_String')->substr($visitor->getHttpReferer(), 0, 250)
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log_url_info'));

        $adapter->insert($this->getTable('log_url_info'), $bind);

        $visitor->setLastUrlId($adapter->lastInsertId($this->getTable('log_url_info')));

        return $this;
    }

    /**
     * Save url info before save
     *
     * @param Magento_Core_Model_Abstract $visitor
     * @return Magento_Log_Model_Resource_Visitor
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $visitor)
    {
        if (!$visitor->getIsNewVisitor()) {
            $this->_saveUrlInfo($visitor);
        }
        return $this;
    }

    /**
     * Actions after save
     *
     * @param Magento_Core_Model_Abstract $visitor
     * @return Magento_Log_Model_Resource_Visitor
     */
    protected function _afterSave(Magento_Core_Model_Abstract $visitor)
    {
        if ($visitor->getIsNewVisitor()) {
            $this->_saveVisitorInfo($visitor);
            $visitor->setIsNewVisitor(false);
        } else {
            $this->_saveVisitorUrl($visitor);
            if ($visitor->getDoCustomerLogin() || $visitor->getDoCustomerLogout()) {
                $this->_saveCustomerInfo($visitor);
            }
            if ($visitor->getDoQuoteCreate() || $visitor->getDoQuoteDestroy()) {
                $this->_saveQuoteInfo($visitor);
            }
        }
        return $this;
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Object $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        parent::_afterLoad($object);
        // Add information about quote to visitor
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('log_quote'), 'quote_id')
            ->where('visitor_id = ?', $object->getId())->limit(1);
        $result = $adapter->query($select)->fetch();
        if (isset($result['quote_id'])) {
            $object->setQuoteId((int) $result['quote_id']);
        }
        return $this;
    }

    /**
     * Saving visitor information
     *
     * @param   Magento_Log_Model_Visitor $visitor
     * @return  Magento_Log_Model_Resource_Visitor
     */
    protected function _saveVisitorInfo($visitor)
    {
        /* @var $stringHelper Magento_Core_Helper_String */
        $stringHelper = Mage::helper('Magento_Core_Helper_String');

        $referer    = $stringHelper->cleanString($visitor->getHttpReferer());
        $referer    = $stringHelper->substr($referer, 0, 255);
        $userAgent  = $stringHelper->cleanString($visitor->getHttpUserAgent());
        $userAgent  = $stringHelper->substr($userAgent, 0, 255);
        $charset    = $stringHelper->cleanString($visitor->getHttpAcceptCharset());
        $charset    = $stringHelper->substr($charset, 0, 255);
        $language   = $stringHelper->cleanString($visitor->getHttpAcceptLanguage());
        $language   = $stringHelper->substr($language, 0, 255);

        $adapter = $this->_getWriteAdapter();
        $data = new \Magento\Object(array(
            'visitor_id'            => $visitor->getId(),
            'http_referer'          => $referer,
            'http_user_agent'       => $userAgent,
            'http_accept_charset'   => $charset,
            'http_accept_language'  => $language,
            'server_addr'           => $visitor->getServerAddr(),
            'remote_addr'           => $visitor->getRemoteAddr(),
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log_visitor_info'));

        $adapter->insert($this->getTable('log_visitor_info'), $bind);
        return $this;
    }

    /**
     * Saving visitor and url relation
     *
     * @param   Magento_Log_Model_Visitor $visitor
     * @return  Magento_Log_Model_Resource_Visitor
     */
    protected function _saveVisitorUrl($visitor)
    {
        $data = new \Magento\Object(array(
            'url_id'        => $visitor->getLastUrlId(),
            'visitor_id'    => $visitor->getId(),
            'visit_time'    => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate()
        ));
        $bind = $this->_prepareDataForTable($data, $this->getTable('log_url'));

        $this->_getWriteAdapter()->insert($this->getTable('log_url'), $bind);
        return $this;
    }

    /**
     * Saving information about customer
     *
     * @param   Magento_Log_Model_Visitor $visitor
     * @return  Magento_Log_Model_Resource_Visitor
     */
    protected function _saveCustomerInfo($visitor)
    {
        $adapter = $this->_getWriteAdapter();

        if ($visitor->getDoCustomerLogin()) {
            $data = new \Magento\Object(array(
                'visitor_id'    => $visitor->getVisitorId(),
                'customer_id'   => $visitor->getCustomerId(),
                'login_at'      => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate(),
                'store_id'      => Mage::app()->getStore()->getId()
            ));
            $bind = $this->_prepareDataForTable($data, $this->getTable('log_customer'));

            $adapter->insert($this->getTable('log_customer'), $bind);
            $visitor->setCustomerLogId($adapter->lastInsertId($this->getTable('log_customer')));
            $visitor->setDoCustomerLogin(false);
        }

        if ($visitor->getDoCustomerLogout() && $logId = $visitor->getCustomerLogId()) {
            $data = new \Magento\Object(array(
                'logout_at' => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate(),
                'store_id'  => (int)Mage::app()->getStore()->getId(),
            ));

            $bind = $this->_prepareDataForTable($data, $this->getTable('log_customer'));

            $condition = array(
                'log_id = ?' => (int) $logId,
            );

            $adapter->update($this->getTable('log_customer'), $bind, $condition);

            $visitor->setDoCustomerLogout(false);
            $visitor->setCustomerId(null);
            $visitor->setCustomerLogId(null);
        }

        return $this;
    }

    /**
     * Saving information about quote
     *
     * @param   Magento_Log_Model_Visitor $visitor
     * @return  Magento_Log_Model_Resource_Visitor
     */
    protected function _saveQuoteInfo($visitor)
    {
        $adapter = $this->_getWriteAdapter();
        if ($visitor->getDoQuoteCreate()) {
            $data = new \Magento\Object(array(
                'quote_id'      => (int) $visitor->getQuoteId(),
                'visitor_id'    => (int) $visitor->getId(),
                'created_at'    => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate()
            ));

            $bind = $this->_prepareDataForTable($data, $this->getTable('log_quote'));

            $adapter->insert($this->getTable('log_quote'), $bind);

            $visitor->setDoQuoteCreate(false);
        }

        if ($visitor->getDoQuoteDestroy()) {
            /**
             * We have delete quote from log because if original quote was
             * deleted and Mysql restarted we will get key duplication error
             */
            $condition = array(
                'quote_id = ?' => (int) $visitor->getQuoteId(),
            );

            $adapter->delete($this->getTable('log_quote'), $condition);

            $visitor->setDoQuoteDestroy(false);
            $visitor->setQuoteId(null);
        }
        return $this;
    }
}
