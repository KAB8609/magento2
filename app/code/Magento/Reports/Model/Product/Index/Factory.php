<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Product\Index;

class Factory
{
    const TYPE_COMPARED = 'compared';
    const TYPE_VIEWED = 'viewed';

    /**
     * @var array
     */
    protected $_typeClasses = array(
        self::TYPE_COMPARED => 'Magento\Reports\Model\Product\Index\Compared',
        self::TYPE_VIEWED => 'Magento\Reports\Model\Product\Index\Viewed'
    );

    /**
     * @var \Magento\Reports\Model\Product\Index\Abstract[]
     */
    protected $_instances;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $type
     * @return \Magento\Reports\Model\Product\Index\AbstractIndex
     * @throws \InvalidArgumentException
     */
    public function get($type)
    {
        if (!isset($this->_instances[$type])) {
            if (!isset($this->_typeClasses[$type])) {
                throw new \InvalidArgumentException("{$type} is not index model");
            }
            $this->_instances[$type] = $this->_objectManager->create($this->_typeClasses[$type]);
        }
        return $this->_instances[$type];
    }
}