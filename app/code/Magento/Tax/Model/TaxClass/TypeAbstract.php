<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Tax Class
 */
namespace Magento\Tax\Model\TaxClass;

abstract class TypeAbstract extends \Magento\Object implements \Magento\Tax\Model\TaxClass\Type\TypeInterface
{
    /**
     * @var \Magento\Tax\Model\Calculation\Rule
     */
    protected $_calculationRule;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType;

    /**
     * @param \Magento\Tax\Model\Calculation\Rule $calculationRule
     * @param array $data
     */
    public function __construct(\Magento\Tax\Model\Calculation\Rule $calculationRule, array $data = array())
    {
        parent::__construct($data);
        $this->_calculationRule = $calculationRule;
    }

    /**
     * Get Collection of Tax Rules that are assigned to this tax class
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getAssignedToRules()
    {
        return $this->_calculationRule
            ->getCollection()
            ->setClassTypeFilter($this->_classType, $this->getId());
    }
}
