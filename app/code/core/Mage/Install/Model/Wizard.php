<?php
/**
 * Installation wizard model
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Wizard
{
    /**
     * Wizard configuration
     *
     * @var array
     */
    protected $_steps = array();
    
    public function __construct() 
    {
        $config = new Varien_Simplexml_Config(Mage::getConfig()->getBaseDir('etc','Mage_Install').DS.'wizard.xml');
        
        foreach ($config->getNode() as $stepName=>$step) {
            $stepObject = new Varien_Object((array)$step);
            $stepObject->setName($stepName);
            $this->_steps[] = $stepObject;
        }
        
        foreach ($this->_steps as $index => $step) {
            if (isset($this->_steps[$index+1])) {
                $this->_steps[$index]->setNextUrl(Mage::getUrl('install', 
                    array(
                        'controller'=>$this->_steps[$index+1]->getController(), 
                        'action'=>$this->_steps[$index+1]->getAction())
                    )
                );
            }
            if (isset($this->_steps[$index-1])) {
                $this->_steps[$index]->setPrevUrl(Mage::getUrl('install', 
                    array(
                        'controller'=>$this->_steps[$index-1]->getController(), 
                        'action'=>$this->_steps[$index-1]->getAction())
                    )
                );
            }
        }
    }
    
    /**
     * Get wizard step by request
     *
     * @param   Mage_Core_Controller_Zend_Request $request
     * @return  Varien_Object || false
     */
    public function getStepByRequest(Mage_Core_Controller_Zend_Request $request)
    {
        foreach ($this->_steps as $step) {
            if ($step->getController() == $request->getControllerName() && $step->getAction() == $request->getActionName()) {
                return $step;
            }
        }
        return false;
    }
    
    /**
     * Get wizard step by name
     *
     * @param   string $name
     * @return  Varien_Object || false
     */
    public function getStepByName($name)
    {
        foreach ($this->_steps as $step) {
            if ($step->getName() == $name) {
                return $step;
            }
        }
        return false;
    }
    
    /**
     * Get all wizard steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }
}