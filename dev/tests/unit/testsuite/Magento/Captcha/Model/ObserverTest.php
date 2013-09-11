<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Captcha_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Captcha\Model\Observer
     */
    protected $_observer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_customerSession;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_captcha;

    protected function setUp()
    {
        $this->_customerSession = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $this->_helper = $this->getMock('Magento\Captcha\Helper\Data', array(), array(), '', false);
        $this->_urlManager = $this->getMock('Magento\Core\Model\Url', array(), array(), '', false);
        $this->_filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_observer = new \Magento\Captcha\Model\Observer(
            $this->_customerSession,
            $this->_helper,
            $this->_urlManager,
            $this->_filesystem
        );
        $this->_captcha = $this->getMock('Magento\Captcha\Model\DefaultModel', array(), array(), '', false);
    }

    public function testCheckContactUsFormWhenCaptchaIsRequiredAndValid()
    {
        $formId = 'contact_us';
        $captchaValue = 'some-value';

        $controller = $this->getMock('Magento\Core\Controller\Varien\Action', array(), array(), '', false);
        $request = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getPost')
            ->with(\Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE, null)
            ->will($this->returnValue(array(
                $formId => $captchaValue,
            )));
        $controller->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $this->_captcha->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));
        $this->_captcha->expects($this->once())
            ->method('isCorrect')
            ->with($captchaValue)
            ->will($this->returnValue(true));
        $this->_helper->expects($this->any())
            ->method('getCaptcha')
            ->with($formId)
            ->will($this->returnValue($this->_captcha));
        $this->_customerSession->expects($this->never())->method('addError');

        $this->_observer->checkContactUsForm(new \Magento\Event\Observer(array('controller_action' => $controller)));
    }

    public function testCheckContactUsFormRedirectsCustomerWithWarningMessageWhenCaptchaIsRequiredAndInvalid()
    {
        $formId = 'contact_us';
        $captchaValue = 'some-value';
        $warningMessage = 'Incorrect CAPTCHA.';
        $redirectRoutePath = 'contacts/index/index';
        $redirectUrl = 'http://magento.com/contacts/';

        $this->_urlManager->expects($this->once())
            ->method('getUrl')
            ->with($redirectRoutePath, null)
            ->will($this->returnValue($redirectUrl));

        $controller = $this->getMock('Magento\Core\Controller\Varien\Action', array(), array(), '', false);
        $request = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $response = $this->getMock('Magento\Core\Controller\Response\Http', array(), array(), '', false);
        $request->expects($this->any())->method('getPost')->with(\Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE,
            null)
            ->will($this->returnValue(array(
                $formId => $captchaValue,
            )));
        $response->expects($this->once())
            ->method('setRedirect')
            ->with($redirectUrl, 302);
        $controller->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $this->_captcha->expects($this->any())->method('isRequired')->will($this->returnValue(true));
        $this->_captcha->expects($this->once())
            ->method('isCorrect')
            ->with($captchaValue)
            ->will($this->returnValue(false));
        $this->_helper->expects($this->any())->method('getCaptcha')
            ->with($formId)
            ->will($this->returnValue($this->_captcha));
        $this->_customerSession->expects($this->once())->method('addError')->with($warningMessage);
        $controller->expects($this->once())->method('setFlag')
            ->with('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);

        $this->_observer->checkContactUsForm(new \Magento\Event\Observer(array('controller_action' => $controller)));
    }

    public function testCheckContactUsFormDoesNotCheckCaptchaWhenItIsNotRequired()
    {
        $this->_helper->expects($this->any())->method('getCaptcha')
            ->with('contact_us')
            ->will($this->returnValue($this->_captcha));
        $this->_captcha->expects($this->any())->method('isRequired')->will($this->returnValue(false));
        $this->_captcha->expects($this->never())->method('isCorrect');

        $this->_observer->checkContactUsForm(new \Magento\Event\Observer());
    }
}
