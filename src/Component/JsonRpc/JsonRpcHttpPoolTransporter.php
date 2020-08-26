<?php
/**
 * JsonRpcHttpPoolTransporter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc;

use GuzzleHttp\Client;
use Hyperf\Utils\Coroutine;
use GuzzleHttp\HandlerStack;
use EyPhp\Framework\Component\Guzzle\CoroutinePoolHandler;

/**
 * description
 */
class JsonRpcHttpPoolTransporter extends JsonRpcHttpTransporter
{
    protected $clientOptions = [
        'timeout' => 5.0,
        'swoole' => [
            'keep_alive' => true,
        ],
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 32,
            'wait_timeout' => 3.0,
            'max_idle_time' => 60.0,
        ],
    ];

    public function getClient(): Client
    {
        $options = $this->clientOptions;
        // Swoole HTTP Client cannot set recv_timeout and connect_timeout options, use timeout.
        $options['timeout'] = $options['recv_timeout'] + $options['connect_timeout'];
        unset($options['recv_timeout'], $options['connect_timeout']);

        $stack = null;
        if (Coroutine::getCid() > 0) {
            $poolOptions = $options['pool'] ?? [];
            $stack = HandlerStack::create(make(CoroutinePoolHandler::class, ['options' => $poolOptions]));
        }

        $config = array_replace(['handler' => $stack], $options);

        return make(Client::class, ['config' => $config]);
    }
}
