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

    /**
     * @var array
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $maps = [];

    protected $locker = false;

    protected $serviceName = ProxyService::class;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->proxyClient = new ProxyClient($this);
    }

    public function get(string $serviceName)
    {
        $proxyClient = clone $this->proxyClient;
        $proxyClient->setProxyService($serviceName);
        return $proxyClient;
    }

    public function setProxyData(string $serviceName, string $func = '', array $argv = [])
    {
        $this->maps[] = [$serviceName, $func, $argv];
    }

    public function wait()
    {
        if ($this->locker) {
            throw new \Exception("Error Processing Request");
        }
        $this->locker = true;
        try {
            $result = $this->formatMaps();
            $ret = [];
            foreach ($result as $groupName => $item) {
                /** @var ServiceClient $proxyClient */
                $proxyClient = make(ServiceClient::class, [
                    'container' => $this->container,
                    'groupName' => $groupName,
                    'serviceName' => $this->serviceName
                ]);
                ksort($item);
                $tRet = $proxyClient->__call(__FUNCTION__, $item);
                $keys = array_keys($item);
                foreach ($tRet as $v) {
                    $ret[array_shift($keys)] = $v;
                }
            }
            ksort($ret);
            return $ret;
        } finally {
            $this->locker = false;
            $this->maps = [];
        }

    }

    protected function formatMaps()
    {
        $maps = $this->maps;
        if (empty($maps)) {
            return [];
        }
        $result = [];
        foreach ($maps as $key => [$serviceName, $func, $args]) {
            /** @var AbstractProxyService $service */
            $service = $this->container->get($serviceName);
            /** @var ServiceClient $serviceClient */
            $serviceClient = $service->getClient();
            $groupName = $serviceClient->getGroupName();
            if (!array_key_exists($groupName, $result)) {
                $result[$groupName] = [];
            }

            $method = $serviceClient->getPathGenerator()
                ->generate($serviceClient->getServiceName(), $func);

            $result[$groupName][$key] = [$method, $args];
        }
        return $result;
    }
}
