<?php

namespace Phppro\Framework\Pipeline;


class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataWithNoKeyReturnsAllData()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');

        $e = new Event('', $data);

        $this->assertEquals($data, $e->getData());
    }
    public function testGetDataWithKeyReturnsOnlyThisKeyValue()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');

        $e = new Event('', $data);

        $this->assertEquals($data['key2'], $e->getData('key2'));
    }
    public function testGetDataWithKeyButKeyDoesNotExistReturnsDefaultValueSpecifiedForThisKey()
    {
        $data = array('key1' => 'value1', 'key2' => 'value2');

        $e = new Event('', $data);

        $this->assertEquals('thisIsTheDefaultValue', $e->getData('key3', 'thisIsTheDefaultValue'));
    }
}
