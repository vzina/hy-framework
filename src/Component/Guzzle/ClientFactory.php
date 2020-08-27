<?php
/**
 * ClientFactory.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Utils\Coroutine;

/**
 * description
 */
class ClientFactory
{
    public function create(array $options = []): Client
    {
        $stack = null;
        if (Coroutine::getCid() > 0) {
            $pool = [];
            $handler = CoroutineHandler::class;
            if (!empty($options['pool'])) {
                $pool = ['options' => $options['pool']];
                $handler = CoroutinePoolHandler::class;
            }
            $stack = HandlerStack::create(make($handler, $pool));
        }

        $config = array_replace(['handler' => $stack], $options);

        return make(Client::class, ['config' => $config]);
    }
}
