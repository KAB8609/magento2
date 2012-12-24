<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_XmlSitemap
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xml Sitemap Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_XmlSitemap_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     * <p>2. Disable Http only</p>
     * <p>3. Disable Secret key</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_http_only');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_push_to_robots');
    }

    protected function tearDownAfterTest()
    {
        $this->closeLastWindow();
    }

    /**
     * <p>Verifying default value of option "Enable Submission to Robots.txt"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5999
     */
    public function withRequiredFieldsDefaultValue()
    {
        //Open XML Sitemap tab
        $loadData = $this->loadDataSet('XmlSitemap', 'admin_disable_push_to_robots');
        $tab = $loadData['tab_1']['tab_name'];
        $this->systemConfigurationHelper()->openConfigurationTab($tab);
        //Verify
        $this->systemConfigurationHelper()->verifyConfigurationOptions($loadData['tab_1']['configuration'], $tab);
    }

    /**
     * <p>Verifying Save process of XML Sitemap</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsSave()
    {
        //Enable push to robots.txt option
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');

        //Create data
        $productData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap');

        //Fill form and save sitemap
        $this->fillFieldset($productData, 'xml_sitemap_create');
        $this->clickButton('save_and_generate');
        $this->pleaseWait();
        $this->validatePage();

        //Check message
        $this->assertMessagePresent('error', 'success_saved_xml_sitemap');

        //Create sitemap link
        $uri = "sitemap.xml";
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl($uri);

        //Create url in format [base url]/robots.txt an read the file
        $uri = "robots.txt";
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $actualRobotsCont = $this->getFile($robotsUrl);

        //Find sitemap link in the robots.txt
        $this->assertContains($sitemapUrl, $actualRobotsCont, 'Stored Robots.txt don\'t have current sitemap!');
    }

    /**
     * <p>Verifying Required field of XML Sitemap</p>
     *
     * @param string $emptyField
     * @param string $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5841
     */
    public function withRequiredFieldsEmpty($emptyField, $messageCount)
    {
        //Create data
        $fieldData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap', array($emptyField => '%noValue%'));

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap', true);
        $this->waitForAjax();

        //Fill form and save sitemap
        $this->fillFieldset($fieldData, 'xml_sitemap_create');
        $this->clickButton('save', false);

        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'xml_sitemap_empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('xml_sitemap_create_filename', 1),
            array ('xml_sitemap_create_path', 1)
        );
    }

    /**
     * <p>Verifying "Edit custom instruction of robots.txt File" push to default Robots.txt</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5876
     */
    public function withRequiredFieldsPushRobots()
    {
        //Enable push to robots.txt option
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create data
        $fieldData = $this->loadDataSet('XmlSitemap', 'admin_xml_sitemap');

        //Create url in format [base url]/robots.txt an read the file
        $uri = 'robots' . '.' . 'txt';
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $order = array("\r\n", "\n", "\r");
        $actualRobots = str_replace($order, '', $this->getFile($robotsUrl));

        //get Robots.txt file and compare with expected content
        $expectedRobots = $fieldData['tab_1']['configuration']['edit_custom_instruction_test'];
        $expectedRobotsTrim = str_replace($order, '', $expectedRobots);

        //Compare file
        $this->assertContains($expectedRobotsTrim, $actualRobots, 'Robots.txt not contained custom instruction!');
    }

    /**
     * <p>Verifying Save process of XML Sitemap with Enable Submission to Robots.txt = "No"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5928
     */
    public function withRequiredFieldsSaveNotPush()
    {
        //Enable Submission to Robots.txt = "No" and save config
        $this->systemConfigurationHelper()->configure('XmlSitemap/admin_disable_push_to_robots');

        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->waitForAjax();
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create data
        $productData = $this->loadDataSet('XmlSitemap', 'new_xml_sitemap');

        //Open XML Sitemap page
        $this->navigate('google_sitemap');

        //Click 'Add Sitemap' button
        $this->clickButton('add_sitemap', true);
        $this->waitForAjax();

        //Fill form and save sitemap
        $this->fillFieldset($productData, 'xml_sitemap_create');
        $this->clickButton('save_and_generate', true);
        $this->pleaseWait();
        $this->validatePage();

        //Check message
        $this->assertMessagePresent('error', 'success_saved_xml_sitemap');

        //Create sitemap link
        $uri = "sitemap.xml";
        $sitemapUrl = $this->xmlSitemapHelper()->getFileUrl($uri);

        //Create url in format [base url]/robots.txt an read the file
        $uri = "robots.txt";
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $actualRobots = $this->getFile($robotsUrl);

        //Find sitemap link in the robots.txt
        $this->assertNotContains($sitemapUrl, $actualRobots, 'Stored Robots.txt have current sitemap!');
    }

    /**
     * <p>Verifying Reset to Default button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5932
     */
    public function withRequiredFieldsEmptyReset()
    {
        //Enable push to robots.txt option
        //$this->navigate('system_configuration');
        //Open Search Engine Robots tab
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        //$this->systemConfigurationHelper()->configure('XmlSitemap/admin_enable_push_to_robots');

        //Fill "Edit custom instruction of robots.txt File" filed and save config
        $this->waitForAjax();
        $this->validatePage();
        $this->fillField('edit_custom_instruction', 'edit_custom_instruction_test');
        $this->clickButton('reset_to_default_robots', false);
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');

        //Create url in format [base url]/robots.txt an read the file
        $uri = 'robots' . '.' . 'txt';
        $robotsUrl = $this->xmlSitemapHelper()->getFileUrl($uri);
        $order = array("\r\n", "\n", "\r");
        $actualRobots = str_replace($order, '', $this->getFile($robotsUrl));

        //get Robots.txt file and compare with expected content
        $expectedRobots =
            "User-agent: *" . "Disallow: /index.php/" . "Disallow: /*?" . "Disallow: /*.js$" . "Disallow: /*.css$"
            . "Disallow: /checkout/" . "Disallow: /tag/" . "Disallow: /app/" . "Disallow: /downloader/"
            . "Disallow: /js/" . "Disallow: /lib/" . "Disallow: /*.php$" . "Disallow: /pkginfo/" . "Disallow: /report/"
            . "Disallow: /skin/" . "Disallow: /var/" . "Disallow: /catalog/" . "Disallow: /customer/"
            . "Disallow: /sendfriend/" . "Disallow: /review/" . "Disallow: /*SID=";
        $expectedRobotsTrim = str_replace($order, '', $expectedRobots);

        //Compare file
        $this->assertEquals($expectedRobotsTrim, $actualRobots, 'Stored Robots.txt not equals to default instructions');
    }
}
