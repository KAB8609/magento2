<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Invitation data resource model
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource;

class Invitation extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Intialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('magento_invitation', 'invitation_id');
        $this->addUniqueField(array(
            'field' => array('customer_id', 'email'),
            'title' => __('Invitation for same email address')
        ));
    }

    /**
     * Save invitation tracking info
     *
     * @param int $inviterId
     * @param int $referralId
     */
    public function trackReferral($inviterId, $referralId)
    {
        $data = array(
                'inviter_id'  => (int)$inviterId,
                'referral_id' => (int)$referralId
            );
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->getTable('magento_invitation_track'),
            $data,
            array_keys($data)
        );
    }
}
