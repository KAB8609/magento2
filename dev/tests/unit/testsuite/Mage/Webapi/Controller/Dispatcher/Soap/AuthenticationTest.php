<?php
/**
 * SOAP web API authentication model.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Soap_AuthenticationTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_roleLocatorMock;

    /** @var Mage_Webapi_Controller_Dispatcher_Soap_Authentication */
    protected $_soapDispatcher;

    /** @var stdClass */
    protected $_usernameToken;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_tokenMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_usernameToken->Username = 'userName';
        $this->_usernameToken->Password = 'password';
        $this->_usernameToken->Created = '2012-12-12';
        $this->_usernameToken->Nonce = 'Nonce';

        $this->_helperMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_tokenFactoryMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Security_UsernameToken_Factory')
            ->setMethods(array('createFromArray'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_tokenMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Security_usernameToken')
            ->disableOriginalConstructor()
            ->setMethods(array('authenticate'))
            ->getMock();
        $this->_tokenFactoryMock
            ->expects($this->once())
            ->method('createFromArray')
            ->will($this->returnValue($this->_tokenMock));
        $this->_roleLocatorMock = $this->getMockBuilder('Mage_Webapi_Model_Authorization_RoleLocator')
            ->setMethods(array('setRoleId'))
            ->disableOriginalConstructor()
            ->getMock();
        $helperFactoryMock = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();
        $helperFactoryMock->expects($this->once())->method('get')->will($this->returnValue($this->_helperMock));
        /** Initialize SUT. */
        $this->_soapDispatcher = new Mage_Webapi_Controller_Dispatcher_Soap_Authentication(
            $helperFactoryMock,
            $this->_tokenFactoryMock,
            $this->_roleLocatorMock
        );
        parent::setUp();
    }

    public function testAuthenticate()
    {
        /** Prepare mocks for SUT constructor. */
        $user = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('getRoleId'))
            ->getMock();
        $roleId = 1;
        $user->expects($this->once())->method('getRoleId')->will($this->returnValue($roleId));
        $this->_tokenMock->expects($this->once())
            ->method('authenticate')
            ->with(
                $this->_usernameToken->Username,
                $this->_usernameToken->Password,
                $this->_usernameToken->Created,
                $this->_usernameToken->Nonce
            )->will($this->returnValue($user));
        $this->_tokenFactoryMock
            ->expects($this->once())
            ->method('createFromArray')
            ->will($this->returnValue($this->_tokenMock));
        $this->_roleLocatorMock->expects($this->once())->method('setRoleId')->with($roleId);
        /** Execute SUT. */
        $this->_soapDispatcher->authenticate($this->_usernameToken);
    }

    /**
     * @dataProvider authenticateExceptionProvider
     */
    public function testAuthenticateWithException($exception, $exceptionMessage)
    {
        /** Prepare mocks for SUT constructor. */
        $this->_tokenMock
            ->expects($this->once())
            ->method('authenticate')
            ->with(
                $this->_usernameToken->Username,
                $this->_usernameToken->Password,
                $this->_usernameToken->Created,
                $this->_usernameToken->Nonce
            )->will($this->throwException($exception));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            $exceptionMessage,
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        /** Execute SUT. */
        $this->_soapDispatcher->authenticate($this->_usernameToken);
    }

    /**
     * Exception data provider for authenticate() method
     *
     * @return array
     */
    public function authenticateExceptionProvider()
    {
        return array(
            'testAuthenticateUsernameTokenInvalidCredentialException.' => array(
                new Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException(),
                'Invalid Username or Password.',
            ),
            'testAuthenticateUsernameTokenNonceUsedException.' => array(
                new Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException(),
                'WS-Security UsernameToken Nonce is already used.',
            ),
            'testAuthenticateUsernameTokenTimestampRefusedException.' => array(
                new Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException(),
                'WS-Security UsernameToken Created timestamp is refused.',
            ),
            'testAuthenticateUsernameTokenInvalidDateException.' => array(
                new Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException(),
                'Invalid UsernameToken Created date.',
            ),
        );
    }
}
