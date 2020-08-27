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
declare (strict_types=1);

namespace EyPhp\Framework\Component\ServiceGovernance\Adapter;

use EyPhp\Framework\Component\Logger\SysLog;
use EyPhp\Framework\Component\ServiceGovernance\Contract\ServiceGovernanceInterface;
use Hyperf\Consul\Agent;
use Psr\Container\ContainerInterface;

/**
 * description
 */
class ConsulService implements ServiceGovernanceInterface
{
    /**
     * @var Agent
     * @author Weijian.Ye <yeweijian@3k.com>
     */
    protected $consulAgent;

    /**
     * @var array
     * @author Weijian.Ye <yeweijian@3k.com> 
     */
    protected $registeredServices = [];

    /**
     * @var \Hyperf\Logger\Logger
     * @author Weijian.Ye <yeweijian@3k.com>
     */
    protected $logger;
    /**
     * @var array
     */
    protected $defaultLoggerContext = [
        'component' => 'service-governance',
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->logger = SysLog::get(SysLog::DEFAULT);
        $this->consulAgent = new Agent(function () use ($container) {
            $config = $container->get(ConfigInterface::class);
            return $container->get(ClientFactory::class)->create([
                'timeout' => 2,
                'base_uri' => $config->get('consul.uri', Agent::DEFAULT_URI),
            ]);
        });
    }

    public function register(string $serviceName, string $address, int $port, string $protocol)
    {
        $this->logger->debug(sprintf('Service %s is registering to the consul.', $serviceName), $this->defaultLoggerContext);
        if ($this->isRegistered($serviceName, $address, $port, $protocol)) {
            $this->logger->info(sprintf('Service %s has been already registered to the consul.', $serviceName), $this->defaultLoggerContext);
            return;
        }

        $nextId = $this->generateId($this->getLastServiceId($serviceName));
        $requestBody = [
            'Name' => $serviceName,
            'ID' => $nextId,
            'Address' => $address,
            'Port' => $port,
            'Meta' => [
                'Protocol' => $protocol,
            ],
        ];
        if ($protocol === 'jsonrpc-http') {
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'HTTP' => "http://{$address}:{$port}/",
                'Interval' => '1s',
            ];
        }
        if (in_array($protocol, ['jsonrpc', 'jsonrpc-tcp-length-check'], true)) {
            $requestBody['Check'] = [
                'DeregisterCriticalServiceAfter' => '90m',
                'TCP' => "{$address}:{$port}",
                'Interval' => '1s',
            ];
        }
        $response = $this->consulAgent->registerService($requestBody);
        if ($response->getStatusCode() === 200) {
            $this->registeredServices[$serviceName][$protocol][$address][$port] = true;
            $this->logger->info(sprintf('Service %s:%s register to the consul successfully.', $serviceName, $nextId), $this->defaultLoggerContext);
        } else {
            $this->logger->warning(sprintf('Service %s register to the consul failed.', $serviceName), $this->defaultLoggerContext);
        }
    }

    public function deregister(string $serviceName, string $address, int $port, string $protocol)
    {
        if ($serviceId = $this->isRegistered($serviceName, $address, $port, $protocol)) {
            $this->registeredServices[$serviceName][$protocol][$address][$port] = false;
            $this->consulAgent->deregisterService($serviceId);
            $this->logger->info(sprintf('Service %s:%s deregister to the consul successfully.', $serviceName, $serviceId), $this->defaultLoggerContext);
        }
    }

    protected function generateId(string $serviceName)
    {
        $exploded = explode('-', $serviceName);
        $length = count($exploded);
        $end = -1;
        if ($length > 1 && is_numeric($exploded[$length - 1])) {
            $end = $exploded[$length - 1];
            unset($exploded[$length - 1]);
        }
        $end = intval($end);
        ++$end;
        $exploded[] = $end;
        return implode('-', $exploded);
    }

    protected function getLastServiceId(string $serviceName)
    {
        $maxId = -1;
        $lastService = $serviceName;
        $services = $this->getServices();
        foreach ($services ?? [] as $id => $service) {
            if (isset($service['Service']) && $service['Service'] === $serviceName) {
                $exploded = explode('-', (string)$id);
                $length = count($exploded);
                if ($length > 1 && is_numeric($exploded[$length - 1]) && $maxId < $exploded[$length - 1]) {
                    $maxId = $exploded[$length - 1];
                    $lastService = $service;
                }
            }
        }
        return $lastService['ID'] ?? $serviceName;
    }

    protected function isRegistered(string $serviceName, string $address, int $port, string $protocol): bool
    {
        if (isset($this->registeredServices[$serviceName][$protocol][$address][$port])) {
            return true;
        }
        $services = $this->getServices();
        $glue = ',';
        $tag = implode($glue, [$serviceName, $address, $port, $protocol]);
        foreach ($services as $serviceId => $service) {
            if (!isset($service['Service'], $service['Address'], $service['Port'], $service['Meta']['Protocol'])) {
                continue;
            }
            $currentTag = implode($glue, [
                $service['Service'],
                $service['Address'],
                $service['Port'],
                $service['Meta']['Protocol'],
            ]);
            if ($currentTag === $tag) {
                $this->registeredServices[$serviceName][$protocol][$address][$port] = $serviceId;
                return true;
            }
        }
        return false;
    }

    protected function getServices()
    {
        $response = $this->consulAgent->services();
        if ($response->getStatusCode() !== 200) {
            $this->logger->warning(sprintf('Service %s register to the consul failed.', $name), $this->defaultLoggerContext);
            return false;
        }
        return $response->json();
    }
}
