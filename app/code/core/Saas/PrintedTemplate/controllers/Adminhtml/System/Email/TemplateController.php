<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Adminhtml/controllers/System/Email/TemplateController.php';

/**
 * Override transactions email template controller to chang ACL for it
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Controllers
 */
class Saas_PrintedTemplate_Adminhtml_System_Email_TemplateController
    extends Mage_Adminhtml_System_Email_TemplateController
{
    /**
     * Ovverride to change ACL
     *
     * @return bool
     * @see Mage_Adminhtml_System_Email_TemplateController::_isAllowed()
     */
    protected function _isAllowed()
    {
        /*
         * @TODO fix isAllowed
         */
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('system/email_template/email_template');
    }
}
