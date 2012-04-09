<?php
/**
 * {license}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 */

/**
 * Saved Payment (CC profiles) controller
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Payment_ProfileController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check whether Payment Profiles functionality enabled
     *
     * @return Enterprise_Pbridge_Payment_ProfileController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Enterprise_Pbridge_Helper_Data')->arePaymentProfilesEnables()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return $this;
    }

    /**
     * Payment Bridge frame with Saved Payment profiles
     */
    public function indexAction()
    {
        if(!Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId()) {
            Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
