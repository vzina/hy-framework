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
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc;

use EyPhp\Framework\Component\JsonRpc\ConsumerProxy\AbstractProxyService;
use EyPhp\Framework\Utils\Parallel;
use Psr\Container\ContainerInterface;

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

    public function wait(): array
    {
        if ($this->locker) {
            throw new \Exception("ProxyManager Processing.");
        }
        $this->locker = true;
        try {
            $result = $this->formatMaps();
            if (empty($result)) {
                return [];
            }
            $func = __FUNCTION__;
            $parallel = new Parallel(count($result));
            foreach ($result as $item) {
                $parallel->add(function () use ($item, $func) {
                    [$groupName, $protocol, $options] = $item['cfg'];
                    /** @var ServiceClient $proxyClient */
                    $proxyClient = make(ServiceClient::class, [
                        'container' => $this->container,
                        'groupName' => $groupName,
                        'serviceName' => $this->serviceName,
                        'protocol' => $protocol,
                        'options' => $options,
                    ]);
                    $ret = [];
                    $data = (array) $proxyClient->__call($func, ['data' => $item['params']]);
                    // 设置结果为调用时的顺序
                    $keys = array_keys($item['params']);
                    foreach ($data as $d) {
                        $ret[array_shift($keys)] = $d;
                    }
                    return $ret;
                });
            }
            $data = $parallel->wait();
            $ret = [];
            // 合并结果，不使用array_merge 因为此函数会使结果重新排序
            foreach ($data as $d) {
                $ret += $d;
            }
            // 按调用的顺序排序
            ksort($ret);

            return $ret;
        } finally {
            $this->locker = false;
            $this->maps = [];
        }
    }

    protected function formatMaps()
    {
        $result = [];
        $maps = $this->maps;
        foreach ($maps as $key => [$serviceName, $func, $args]) {
            /** @var AbstractProxyService $service */
            $service = $this->container->get($serviceName);
            /** @var ServiceClient $serviceClient */
            $serviceClient = $service->getRpcClient();
            $groupName = $serviceClient->getGroupName();
            $protocol = $serviceClient->getProtocol();
            $options = $serviceClient->getOpts();
            // 解决分组名相同问题
            [$prefix] = explode('\\', trim($serviceName, '\\'), 2);
            $index = "{$prefix}:{$groupName}:{$protocol}";
            if (!array_key_exists($index, $result)) {
                $result[$index] = [];
            }
            // 分组配置，只获取一次即可
            if (!array_key_exists('cfg', $result[$index])) {
                $result[$index]['cfg'] = [$groupName, $protocol, $options];
            }
            // 服务数据
            $method = $serviceClient->getPathGenerator()->generate($serviceClient->getServiceName(), $func);
            $result[$index]['params'][$key] = [$method, $args];
        }
        return $result;
    }
}
