<?php
/**
 * CoroutinePoolHandler.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Guzzle;

use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Swoole\Coroutine\Http\Client;
use Hyperf\Pool\SimplePool\PoolFactory;

/**
 * description
 */
class CoroutinePoolHandler extends CoroutineHandler
{
    /**
     * @var PoolFactory
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $poolFactory;
    protected $options = [];

    public function __construct(PoolFactory $poolFactory, array $options = [])
    {
        $this->poolFactory = $poolFactory;
        $this->options = $options;
    }

    protected function getConnection(string $scheme, string $host, int $port, bool $ssl): Client
    {
        $key = $this->getContextName($scheme, $host, $port);
        return Context::getOrSet($key, function () use ($host, $port, $ssl, $key) {
            $pool = $this->poolFactory->get($key, function() use ($host, $port, $ssl) {
                return new Client($host, $port, $ssl);
            }, $this->options);
            $connection = $pool->get();
            Coroutine::defer(function () use ($connection) {
                $connection->release();
            });
            return $connection->getConnection();
        });
    }
}
