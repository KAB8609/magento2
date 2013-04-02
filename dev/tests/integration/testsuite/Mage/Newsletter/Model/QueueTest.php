<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Newsletter_Model_QueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Newsletter/_files/queue.php
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     * @magentoConfigFixture frontend/design/theme/full_name default/demo_blue
     * @magentoConfigFixture fixturestore_store general/locale/code  de_DE
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriber()
    {
        $collection = new Mage_Core_Model_Resource_Theme_Collection;
        $themeId = $collection->getThemeByFullPath('frontend/default/demo')->getId();
        Mage::app()->getStore('fixturestore')->setConfig('design/theme/theme_id', $themeId);

        $subscriberOne = $this->getMock('Zend_Mail', array('send', 'setBodyHTML'), array('utf-8'));
        $subscriberOne->expects($this->any())->method('send');
        $subscriberTwo = clone $subscriberOne;
        $subscriberOne->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/default/demo_blue/en_US/images/logo.gif')
        );
        $subscriberTwo->expects($this->once())->method('setBodyHTML')->with(
            $this->stringEndsWith('/static/frontend/default/demo/de_DE/images/logo.gif')
        );

        $emailTemplate = $this->getMock('Mage_Core_Model_Email_Template', array('_getMail'), array(), '', false);
        $emailTemplate->expects($this->exactly(2))->method('_getMail')->will($this->onConsecutiveCalls(
            $subscriberOne, $subscriberTwo
        ));

        $queue = Mage::getModel('Mage_Newsletter_Model_Queue',
            array('data' => array('email_template' => $emailTemplate))
        );
        $queue->load('Subject', 'newsletter_subject'); // fixture
        $queue->sendPerSubscriber();
    }

    /**
     * @magentoDataFixture Mage/Core/_files/frontend_default_theme.php
     * @magentoDataFixture Mage/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriberProblem()
    {
        $mail = $this->getMock('Zend_Mail', array('send'), array('utf-8'));
        $brokenMail = $this->getMock('Zend_Mail', array('send'), array('utf-8'));
        $errorMsg = md5(microtime());
        $brokenMail->expects($this->any())->method('send')->will($this->throwException(new Exception($errorMsg, 99)));
        $template = $this->getMock('Mage_Core_Model_Email_Template', array('_getMail'), array(), '', false);
        $template->expects($this->any())->method('_getMail')->will($this->onConsecutiveCalls($mail, $brokenMail));

        $queue = Mage::getModel('Mage_Newsletter_Model_Queue',
            array('data' => array('email_template' => $template))
        );
        $queue->load('Subject', 'newsletter_subject'); // fixture
        $problem = Mage::getModel('Mage_Newsletter_Model_Problem');
        $problem->load($queue->getId(), 'queue_id');
        $this->assertEmpty($problem->getId());

        $queue->sendPerSubscriber();

        $problem->load($queue->getId(), 'queue_id');
        $this->assertNotEmpty($problem->getId());
        $this->assertEquals(99, $problem->getProblemErrorCode());
        $this->assertEquals($errorMsg, $problem->getProblemErrorText());
    }
}
