<?php

defined('UNIT_TESTS_ROOT') || require __DIR__ . '/bootstrap.php';

class CounterAdapterRedisTest extends TestCase
{
    public $di;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->di = new ManaPHP\Di\FactoryDefault();
        $this->di->setShared('redis', function () {
            $redis = new \Redis();
            $redis->connect('localhost');
            return $redis;
        });
    }

    public function test_get()
    {
        $counter = new ManaPHP\Counter\Adapter\Redis();
        $counter->setDependencyInjector($this->di);

        $counter->delete('c', '1');

        $this->assertEquals(0, $counter->get('c', '1'));
        $counter->increment('c', '1');
        $this->assertEquals(1, $counter->get('c', '1'));
    }

    public function test_increment()
    {
        $counter = new ManaPHP\Counter\Adapter\Redis();
        $counter->setDependencyInjector($this->di);

        $counter->delete('c', '1');
        $this->assertEquals(2, $counter->increment('c', '1', 2));
        $this->assertEquals(22, $counter->increment('c', '1', 20));
        $this->assertEquals(2, $counter->increment('c', '1', -20));
    }

    public function test_delete()
    {
        $counter = new ManaPHP\Counter\Adapter\Redis();
        $counter->setDependencyInjector($this->di);

        $counter->delete('c', '1');

        $counter->increment('c', '1', 1);
        $counter->delete('c', '1');
    }
}