<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class ClassFirst
{

}
class ClassSecond
{

}
class ClassThird
{

}
class ClassD
{

}
interface InterfaceFirst
{

}
class ImplementationOfInterfaceFirst implements InterfaceFirst
{

}
interface InterfaceSecond
{

}
class ImplementationOfInterfaceSecond implements InterfaceSecond
{

}
class ContextFirst implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var ClassFirst
     */
    protected $_exA;

    /**
     * @var ClassSecond
     */
    protected $_exB;

    /**
     * @var ClassThird
     */
    protected $_exC;

    /**
     * @var InterfaceFirst
     */
    protected $_interfaceA;

    /**
     * @var ImplementationOfInterfaceSecond
     */
    protected $_implOfBInterface;

    public function __construct(
        \ClassFirst $exA, \ClassSecond $exB, \ClassThird $exC,
        \InterfaceFirst $interfaceA,
        \ImplementationOfInterfaceSecond $implOfBInterface
    ) {
        $this->_exA = $exA;
        $this->_exB = $exB;
        $this->_exC = $exC;
        $this->_interfaceA = $interfaceA;
        $this->_implOfBInterface = $implOfBInterface;
    }

}

class ClassArgumentAlreadyInjectedInContext
{
    /**
     * @var ContextFirst
     */
    protected $_context;

    /**
     * @var ClassFirst
     */
    protected $_exA;

    public function __construct(\ContextFirst $context, \ClassFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWithInterfaceImplementation
{
    /**
     * @var ContextFirst
     */
    protected $_context;

    /**
     * @var ImplementationOfInterfaceFirst
     */
    protected $_exA;

    public function __construct(\ContextFirst $context, \ImplementationOfInterfaceFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}

class ClassArgumentWithInterface
{
    /**
     * @var ContextFirst
     */
    protected $_context;

    /**
     * @var InterfaceSecond
     */
    protected $_exB;

    public function __construct(\ContextFirst $context, \InterfaceSecond $exB)
    {
        $this->_context = $context;
        $this->_exB = $exB;
    }
}

class ClassArgumentWithAlreadyInjectedInterface
{
    /**
     * @var ContextFirst
     */
    protected $_context;

    /**
     * @var InterfaceFirst
     */
    protected $_exA;

    public function __construct(\ContextFirst $context, \InterfaceFirst $exA)
    {
        $this->_context = $context;
        $this->_exA = $exA;
    }
}