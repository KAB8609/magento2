<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Di;

interface DiInterface
{
    /**
     * @param string $param
     * @return mixed
     */
    public function wrap($param);
}