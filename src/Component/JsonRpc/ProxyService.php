<?php
/**
 * ProxyService.php
 * PHP version 7
 *
 * @package hyperf-skeleton
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc;

use Hyperf\JsonRpc\PathGenerator;
use Hyperf\RpcServer\Router\Router;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\RpcServer\Router\DispatcherFactory;

/**
 * description
 */
class ProxyService
{
    const PROXY_SERVER_NAME = 'jsonrpc-http';

    /**
     * @var ContainerInterface
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function wait(array $data)
    {
        $factory = make(DispatcherFactory::class, ['pathGenerator' => make(PathGenerator::class)]);
        $dispatcher = $factory->getDispatcher(static::PROXY_SERVER_NAME);
        $ret = [];
        foreach ($data as $key => [$func, $params]) {
            $routes = $dispatcher->dispatch('POST', $func);
            $dispatched = new Dispatched($routes);

            // 检查路由是否正确
            if (! $dispatched->isFound()) {
                throw new \Exception("Method Not Found:{$func}");
            }

            // 执行请求
            [$ct, $ac] = $dispatched->handler->callback;
            $ret[$key] = $this->container->get($ct)->{$ac}(...$params);
        }
        return $ret;
    }

    /**
     * registerRoute
     * @return void
     * @author weijian.ye <yeweijian299@163.com>
     */
    public static function registerRoute()
    {
        if (env('JSON_RPC_PROXY_ENABLE', false)) {
            // json rpc代理路由
            Router::addServer(static::PROXY_SERVER_NAME, function () {
                Router::add('/proxy/wait', __CLASS__ . '@wait');
            });
        }
    }
}
