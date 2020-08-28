<?php
/**
 * ConsulAgent.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\ServiceGovernance\Service;

use EyPhp\Framework\Component\Guzzle\ClientFactory;
use EyPhp\Framework\Component\Logger\SysLog;
use EyPhp\Framework\Component\ServiceGovernance\Contract\ServiceGovernanceInterface;
use Hyperf\Consul\Agent;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

/**
 * description
 */
class ConsulService implements ServiceGovernanceInterface
{
    /**
     * @var Agent
     */
    protected $consulAgent;

    /**
     * @var array
     */
    protected $registeredServices = [];

    /**
     * @var \Hyperf\Logger\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $options;

    public function __construct(ContainerInterface $container, array $options = [])
    {
        $this->logger = SysLog::get(SysLog::DEFAULT);
        $this->options = $options;
        $this->consulAgent = new Agent(function () use ($container) {
            $config = $container->get(ConfigInterface::class);
            return $container->get(ClientFactory::class)->create([
                'timeout' => $this->options['timeout'] ?? 2,
                'base_uri' => $this->options['register'] ?? $config->get('consul.uri', Agent::DEFAULT_URI),
            ]);
        });
    }

    public function register(string $serviceName, string $host, int $port, string $protocol)
    {
        $this->logger->debug(sprintf('Service %s is registering to the consul.', $serviceName));
        if ($this->isRegistered($serviceName, $host, $port, $protocol)) {
            $this->logger->info(sprintf('Service %s has been already registered to the consul.', $serviceName));
            return;
        }

        $nextId = $this->generateId($serviceName, $host, $port, $protocol);
        $requestBody = [
            'Name' => $serviceName,
            'ID' => $nextId,
            'Address' => $host,
            'Port' => $port,
            'Meta' => [
                'Protocol' => $protocol,
            ],
        ];
        if ($protocol === 'jsonrpc-http') {
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'HTTP' => "http://{$host}:{$port}/",
                'Interval' => '1s',
            ];
        }
        if (in_array($protocol, ['jsonrpc', 'jsonrpc-tcp-length-check'], true)) {
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'TCP' => "{$host}:{$port}",
                'Interval' => '1s',
            ];
        }
        $response = $this->consulAgent->registerService($requestBody);
        if ($response->getStatusCode() === 200) {
            $this->registeredServices[$serviceName][$protocol][$host][$port] = $nextId;
            $this->logger->info(sprintf('Service %s:%s register to the consul successfully.', $serviceName, $nextId));
        } else {
            $this->logger->warning(sprintf('Service %s register to the consul failed.', $serviceName));
        }
    }

    public function deregister(string $serviceName, string $host, int $port, string $protocol)
    {
        if ($this->isRegistered($serviceName, $host, $port, $protocol)) {
            $serviceId = $this->registeredServices[$serviceName][$protocol][$host][$port];
            $this->registeredServices[$serviceName][$protocol][$host][$port] = false;
            $this->consulAgent->deregisterService($serviceId);
            $this->logger->info(sprintf('Service %s:%s deregister to the consul successfully.', $serviceName, $serviceId));
        }
    }

    protected function generateId(string $serviceName, string $host, int $port, string $protocol)
    {
        return md5("{$serviceName}-{$host}-{$port}-{$protocol}");
    }

    protected function isRegistered(string $serviceName, string $host, int $port, string $protocol): bool
    {
        if (isset($this->registeredServices[$serviceName][$protocol][$host][$port])) {
            return true;
        }
        $response = $this->consulAgent->services();
        if ($response->getStatusCode() !== 200) {
            $this->logger->warning(sprintf('Service %s register to the consul failed.', $serviceName));
            return false;
        }
        $services = $response->json();
        $glue = ',';
        $tag = implode($glue, [$serviceName, $host, $port, $protocol]);
        foreach ($services as $service) {
            if (!isset($service['Service'], $service['Address'], $service['Port'], $service['Meta']['Protocol'], $service['ID'])) {
                continue;
            }
            $currentTag = implode($glue, [
                $service['Service'],
                $service['Address'],
                $service['Port'],
                $service['Meta']['Protocol'],
            ]);
            if ($currentTag === $tag) {
                $this->registeredServices[$serviceName][$protocol][$host][$port] = $service['ID'];
                return true;
            }
        }
        return false;
    }
}
