<?php
/**
 * ServiceManager.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\ServiceGovernance;

use EyPhp\Framework\Component\ServiceGovernance\Contract\ServiceGovernanceInterface;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * description
 */
class ServiceManager
{
    /**
     * $container
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
    }

    public function register()
    {
        $servers = $this->getServers();
        foreach ($servers as $serverName => [$protocol, $host, $port, $type, $options]) {
            $this->getAgentService($type, $options)->register($serverName, $host, $port, $protocol);
        }
    }

    public function deregister()
    {
        $servers = $this->getServers();
        foreach ($servers as $serverName => [$protocol, $host, $port, $type, $options]) {
            $this->getAgentService($type, $options)->deregister($serverName, $host, $port, $protocol);
        }
    }

    protected function getAgentService(string $service, array $options = []): ?ServiceGovernanceInterface
    {
        $agentService = make($service, ['options' => $options]);
        if ($agentService instanceof ServiceGovernanceInterface) {
            return $agentService;
        }
        throw new RuntimeException(sprintf("Invalid publish.type [%s], does not implement %s", $service, ServiceGovernanceInterface::class));
    }

    protected function getServers(): array
    {
        $result = [];
        $servers = $this->config->get('server.servers', []);
        foreach ($servers as $server) {
            if (!isset($server['name'], $server['publish'], $server['host'], $server['port'])) {
                continue;
            }
            if (empty($server['name']) || empty($server['publish'])) {
                throw new \InvalidArgumentException('Invalid server name');
            }
            $protocol = $server['name'];
            $host = $server['host'];
            if (in_array($host, ['0.0.0.0', 'localhost'])) {
                $host = $this->getInternalIp();
            }
            if (!filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \InvalidArgumentException(sprintf('Invalid host %s', $host));
            }
            $port = $server['port'];
            if (!is_numeric($port) || ($port < 0 || $port > 65535)) {
                throw new \InvalidArgumentException(sprintf('Invalid port %s', $port));
            }
            $port = (int) $port;
            if (empty($server['publish']['name'])) {
                throw new \InvalidArgumentException('Invalid publish.name');
            }

            if (empty($server['publish']['type'])) {
                throw new \InvalidArgumentException('Invalid publish.type');
            }

            if (!class_exists($server['publish']['type'])) {
                throw new \InvalidArgumentException(sprintf('Invalid publish.type [%s]', $server['publish']['type']));
            }

            $result[$server['publish']['name']] = [$protocol, $host, $port, $server['publish']['type'], $server['publish']['options'] ?? []];
        }
        return $result;
    }

    protected function getInternalIp(): string
    {
        $ips = swoole_get_local_ip();
        if (is_array($ips) && !empty($ips)) {
            return current($ips);
        }
        /** @var mixed|string $ip */
        $ip = gethostbyname(gethostname());
        if (is_string($ip)) {
            return $ip;
        }
        throw new \RuntimeException('Can not get the internal IP.');
    }
}
