<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Model\System\Message\Media;

abstract class SynchronizationAbstract
    implements \Magento\AdminNotification\Model\System\MessageInterface
{
    /**
     * @var \Magento\Core\Model\File\Storage\Flag
     */
    protected $_syncFlag;

    /**
     * Message identity
     *
     * @var string
     */
    protected $_identity;

    /**
     * Is displayed flag
     *
     * @var bool
     */
    protected $_isDisplayed = null;

    /**
     * @param \Magento\Core\Model\File\Storage $fileStorage
     */
    public function __construct(\Magento\Core\Model\File\Storage $fileStorage)
    {
        $this->_syncFlag = $fileStorage->getSyncFlag();
    }

    /**
     * Check if message should be displayed
     *
     * @return bool
     */
    protected abstract function _shouldBeDisplayed();

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        if (null === $this->_isDisplayed) {
            $output = $this->_shouldBeDisplayed();
            if ($output) {
                $this->_syncFlag->setState(\Magento\Core\Model\File\Storage\Flag::STATE_NOTIFIED);
                $this->_syncFlag->save();
            }
            $this->_isDisplayed = $output;
        }
        return $this->_isDisplayed;
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_MAJOR;
    }
}
