<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Action\Title
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\App\Action\Title();
    }

    public function testAddPrependFalse()
    {
        $this->_model->add('First Title');
        $this->_model->add('Second Title');
        $actual = $this->_model->get();
        $expected = array('First Title', 'Second Title');

        $this->assertEquals($expected, $actual);
    }

    public function testAddPrependTrue()
    {
        $this->_model->add('First Title');
        $this->_model->add('Second Title', true);
        $actual = $this->_model->get();
        $expected = array('Second Title', 'First Title');

        $this->assertEquals($expected, $actual);
    }

}
