<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PromotionPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Promotion Permissions Data Helper
 *
 * @category    Enterprise
 * @package     Enterprise_PromotionPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PromotionPermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Path to node in ACL that specifies edit permissions for catalog rules
     *
     * Used to check if admin has permission to edit catalog rules
     */
    const EDIT_PROMO_CATALOGRULE_ACL_PATH = 'promo/catalog/edit';

    /**
     * Path to node in ACL that specifies edit permissions for sales rules
     *
     * Used to check if admin has permission to edit sales rules
     */
    const EDIT_PROMO_SALESRULE_ACL_PATH = 'promo/quote/edit';

    /**
     * Path to node in ACL that specifies edit permissions for reminder rules
     *
     * Used to check if admin has permission to edit reminder rules
     */
    const EDIT_PROMO_REMINDERRULE_ACL_PATH = 'promo/enterprise_reminder/edit';

    /**
     * Check if admin has permissions to edit catalog rules
     *
     * @return boolean
     */
    public function getCanAdminEditCatalogRules()
    {
        return (boolean) Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed(self::EDIT_PROMO_CATALOGRULE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit sales rules
     *
     * @return boolean
     */
    public function getCanAdminEditSalesRules()
    {
        return (boolean) Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed(self::EDIT_PROMO_SALESRULE_ACL_PATH);
    }

    /**
     * Check if admin has permissions to edit reminder rules
     *
     * @return boolean
     */
    public function getCanAdminEditReminderRules()
    {
        return (boolean) Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed(self::EDIT_PROMO_REMINDERRULE_ACL_PATH);
    }
}
