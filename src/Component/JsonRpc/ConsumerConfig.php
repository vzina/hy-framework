<?php
/**
 * ConsumerConfigFactory.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc;

use Exception;
use Hyperf\Utils\Composer;
use Hyperf\Utils\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * description
 */
class ConsumerConfig
{
    const SERVER_PROTOCOL = 'jsonrpc-http';
    const SERVER_LOAD_BALANCER = 'random';
    const CONTRACT_DIR_NAME = 'Contract';

    /**
     * @var Filesystem
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem;
    }

    public static function make()
    {
        return (new static())->build();
    }

    protected function build()
    {
        $ret = [];
        $services = $this->loadCfg();
        foreach ($services as $service) {
            if (env('SERVICE_GOVERNANCE_ENABLE', false)) {
                $consumers = $this->getConsulConsumer($service);
            } else {
                $consumers = $this->getNodeConsumer($service);
            }
            // 过滤空配置
            if ($consumers) {
                $ret['consumers'][] = $consumers;
            }
        }
        return $ret;
    }

    protected function getConsulConsumer(array $service)
    {
        $registryProtocol = (string) env('SERVICE_GOVERNANCE_REGISTRY_PROTOCOL', 'consul');
        $registryAddress = (string) env('SERVICE_GOVERNANCE_REGISTRY_ADDRESS');
        if (empty($registryProtocol) || empty($registryAddress)) {
            throw new Exception("未设置环境变量[SERVICE_GOVERNANCE_REGISTRY_PROTOCOL|SERVICE_GOVERNANCE_REGISTRY_ADDRESS]");
        }
        return [
            'name' => (string) $service['name'],
            'protocol' => (string) $service['protocol'],
            'load_balancer' => (string) $service['load_balancer'],
            'auto_services' => (array) $service['auto_services'],
            'registry' => [
                'protocol' => $registryProtocol,
                'address' => $registryAddress,
            ],
            'options' => $this->getOptionsConfig((string) $service['prefix']),
        ];
    }

    protected function getNodeConsumer(array $service): array
    {
        $nodes = [];
        $prefix = (string) $service['prefix'];
        $envName = $prefix . '_NODES';
        $envSet = (string) env($envName);
        // 不处理空配置
        if (empty($envSet)) {
            return [];
        }
        $nodeList = explode('|', $envSet);
        foreach ($nodeList as $node) {
            $nodeArr = explode(':', $node, 2);
            if (sizeof($nodeArr) != 2) {
                throw new Exception(sprintf('环境配置[%s]格式异常', $envName));
            }
            $nodes[] = ['host' => $nodeArr[0], 'port' => (int) $nodeArr[1]];
        }

        $result = [
            'nodes' => $nodes,
            'options' => $this->getOptionsConfig($prefix),
        ];

        return array_merge($service, $result);
    }

    protected function getOptionsConfig(string $prefix)
    {
        return [
            'timeout' => 5,
            'remove_disable_node' => false,
            'swoole' => [
                'keep_alive' => true,
            ],
            'pool' => [
                'min_connections' => (int) env($prefix . '_MIN_CONNECTIONS', 1),
                'max_connections' => (int) env($prefix . '_MAX_CONNECTIONS', 32),
                'wait_timeout' => (int) env($prefix . '_WAIT_TIMEOUT', 3.0),
                'max_idle_time' => (int) env($prefix . '_MAX_IDLE_TIME', 60),
            ],
        ];
    }

    protected function loadCfg(): array
    {
        $services = Composer::getMergedExtra('eyphp')['config'] ?? [];
        return $this->loadServices($services);
    }

    protected function loadServices(array $services): array
    {
        $serviceConfigs = [];
        foreach ($services as $service) {
            if (is_string($service) && class_exists($service) && method_exists($service, '__invoke')) {
                $basePath = (new $service())();
                $contractDirName = defined("{$service}::CONTRACT_DIR_NAME") ? constant("{$service}::CONTRACT_DIR_NAME") : static::CONTRACT_DIR_NAME;
                $servicePath = $basePath . '/' . $contractDirName;
                if (!is_dir($servicePath)) {
                    continue;
                }
                $paths = $this->filesystem->directories($servicePath);
                $nsRoot = substr($service, 0, strrpos($service, '\\'));
                $protocol = defined("{$service}::SERVER_PROTOCOL") ? constant("{$service}::SERVER_PROTOCOL") : static::SERVER_PROTOCOL;
                $loadBalancer = defined("{$service}::SERVER_LOAD_BALANCER") ? constant("{$service}::SERVER_LOAD_BALANCER") : static::SERVER_LOAD_BALANCER;
                foreach ($paths as $path) {
                    $name = basename($path);
                    $groupCfg = [
                        // 配置前缀，如：EYPHP_SERVICE_USER  EYPHP_SERVICE：服务的根命名空间，USER：服务分组名
                        'prefix' => strtoupper(strtr($nsRoot, '\\', '_') . '_' . preg_replace('/([a-z])([A-Z])/', "$1_$2", $name)),
                        'name' => $name,
                        'protocol' => $protocol,
                        'load_balancer' => $loadBalancer,
                        'auto_services' => [],
                    ];

                    $files = $this->filesystem->files($path);
                    foreach ($files as $file) {
                        /** @var SplFileInfo $file */
                        $groupCfg['auto_services'][] = $nsRoot . strtr(strstr($file->getPathname(), '.', true), [$basePath => '', '/' => '\\']);

                    }
                    $serviceConfigs[] = $groupCfg;
                }
            }
        }

        return $serviceConfigs;
    }
}
