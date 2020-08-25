<?php
/**
 * ProxyManager.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc;

use Psr\Container\ContainerInterface;
use EyPhp\Framework\Component\JsonRpc\ConsumerProxy\AbstractProxyService;

/**
 * description
 */
class ProxyManager
{
    /**
     * @var ContainerInterface
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $container;

    /**
     * @var ProxyClient
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $proxyClient;

    protected $data = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $serviceName)
    {
        $this->data[$serviceName] = [];
        return $this;
    }

    public function __call($method, $argv)
    {
        $service = $this->client->getServiceName();
        $func = $this->client->getPathGenerator()->generate($service, $method);
        $this->data[] = [$func, $argv];
    }



    public function wait()
    {
        if ($this->proxyClient) {
            $result = $this->proxyClient->wait();
            $this->proxyClient = null; // 重置
            return $result;
        }
    }
}
