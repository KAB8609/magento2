<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Paypal_Adminhtml_Paypal_ReportsControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoConfigFixture current_store paypal/fetch_reports/active 1
     * @magentoConfigFixture current_store paypal/fetch_reports/ftp_ip 127.0.0.1
     * @magentoConfigFixture current_store paypal/fetch_reports/ftp_path /tmp
     * @magentoConfigFixture current_store paypal/fetch_reports/ftp_login login
     * @magentoConfigFixture current_store paypal/fetch_reports/ftp_password password
     * @magentoConfigFixture current_store paypal/fetch_reports/ftp_sandbox 0
     * @magentoDbIsolation enabled
     */
    public function testFetchAction()
    {
        $this->dispatch('backend/admin/paypal_reports/fetch');
        $this->assertAdminMessages(
            $this->equalTo(array("Failed to fetch reports from 'login@127.0.0.1'.")), Mage_Core_Model_Message::ERROR
        );
    }
}
