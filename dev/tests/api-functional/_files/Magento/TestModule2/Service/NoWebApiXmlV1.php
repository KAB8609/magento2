<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

class NoWebApiXmlV1 implements \Magento\TestModule2\Service\NoWebApiXmlV1Interface
{
    /**
     * @param array $request
     * @return array
     */
    public function item($request)
    {
        return array(
            'id' => $request['id']
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function items()
    {
        return array(
            array(
                'id' => 1,
                'name' => 'testItem1'
            ),
            array(
                'id' => 2,
                'name' => 'testItem2'
            )
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function create($request)
    {
        $result = array(
            'id' => rand(),
            'name' => $request['name']
        );
        return $result;
    }

    /**
     * @param array $request
     * @return array
     */
    public function update($request)
    {
        return array(
            'id' => $request['id']
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function remove($request)
    {
        return array(
            'id' => $request['id']
        );
    }
}