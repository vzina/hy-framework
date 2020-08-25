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
    /**
     * Undocumented variable
     *
     * @var Filesystem
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $filesystem;

    protected $protocol = 'jsonrpc-http';
    protected $loadBalancer = 'random';

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
        $services = $this->loadCfg();
        $ret = [];
        foreach ($services as $service) {
            if (env('REGISTRY_ENABLE', false)) {
                $consumers = $this->getConsulConsumer($service);
            }{
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
        $name = (string) $service['name'];
        $envSet = (string)env('CONSUL_URI');
        if (empty($envSet)) {
            return [];
        }
        return [
            'name' => $name,
            'protocol' => $this->protocol,
            'load_balancer' => $this->loadBalancer,
            'registry' => [
                'protocol' => 'consul',
                'address' => $envSet,
            ],
            'pool' => $this->getPoolConfig($this->getPrefixByName($name)),
        ];
    }

    protected function getNodeConsumer(array $service): array
    {
        $nodes = [];
        $prefix = $this->getPrefixByName((string) $service['name']);
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
                throw new Exception('[ServiceConsumerConfig.get]Error Node Conf ' . $envName);
            }
            $nodes[] = ['host' => $nodeArr[0], 'port' => (int) $nodeArr[1]];
        }

        $result = [
            'protocol' => $this->protocol,
            'load_balancer' => $this->loadBalancer,
            'nodes' => $nodes,
            'pool' => $this->getPoolConfig($prefix),
        ];

        return array_merge($service, $result);
    }

    protected function getPoolConfig(string $prefix)
    {
        return [
            'min_connections' => (int) env($prefix . '_MIN_CONNECTIONS', 1),
            'max_connections' => (int) env($prefix . '_MAX_CONNECTIONS', 32),
            'wait_timeout' => (int) env($prefix . '_WAIT_TIMEOUT', 3.0),
            'max_idle_time' => (int) env($prefix . '_MAX_IDLE_TIME', 60),
        ];
    }

    protected function getPrefixByName(string $name): string
    {
        return strtoupper(preg_replace('/([a-z])([A-Z])/', "$1_$2", $name));
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
                $servicePath = $basePath . '/Contract';
                if (!is_dir($servicePath)) {
                    continue;
                }
                $paths = $this->filesystem->directories($servicePath);
                foreach ($paths as $path) {
                    $info = [
                        'name' => basename($path),
                        'auto_services' => [],
                    ];

                    $files = $this->filesystem->files($path);
                    foreach ($files as $file) {
                        /** @var SplFileInfo $file */
                        $info['auto_services'][] = substr($service, 0, strrpos($service, '\\')) . strtr(
                            strstr($file->getPathname(), '.', true), [$basePath => '', '/' => '\\']
                        );
                    }
                    $serviceConfigs[] = $info;
                }
            }
        }

        return $serviceConfigs;
    }
}
