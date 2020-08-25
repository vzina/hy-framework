<?php
/**
 * RpcClient.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc;

use RuntimeException;
use Hyperf\LoadBalancer\Node;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Hyperf\LoadBalancer\LoadBalancerInterface;
use Hyperf\RpcClient\ServiceClient as BaseServiceClient;

/**
 * description
 */
class ServiceClient extends BaseServiceClient
{
    /**
     * 服务组名称
     *
     * @var string
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $groupName = '';

    public function __construct(ContainerInterface $container, string $groupName, string $serviceName, string $protocol = 'jsonrpc-http', array $options = [])
    {
        $this->groupName = $groupName;
        parent::__construct($container, $serviceName, $protocol, $options);
    }


    protected function createLoadBalancer(array $nodes, callable $refresh = null): LoadBalancerInterface
    {
        $loadBalancer = $this->loadBalancerManager->getInstance($this->groupName, $this->loadBalancer)->setNodes($nodes);
        $refresh && $loadBalancer->refresh($refresh);
        return $loadBalancer;
    }


    protected function getConsumerConfig(): array
    {
        if (! $this->container->has(ConfigInterface::class)) {
            throw new RuntimeException(sprintf('The object implementation of %s missing.', ConfigInterface::class));
        }

        $config = $this->container->get(ConfigInterface::class);

        // According to the registry config of the consumer, retrieve the nodes.
        $consumers = $config->get('services.consumers', []);
        $config = [];
        foreach ($consumers as $consumer) {
            if (isset($consumer['name']) && $consumer['name'] === $this->groupName) {
                $config = $consumer;
                break;
            }
        }

        return $config;
    }

    protected function getNodesFromConsul(array $config): array
    {
        $health = $this->createConsulHealth($config);
        $services = $health->service($this->groupName)->json();
        $nodes = [];
        foreach ($services as $node) {
            $passing = true;
            $service = $node['Service'] ?? [];
            $checks = $node['Checks'] ?? [];

            if (isset($service['Meta']['Protocol']) && $this->protocol !== $service['Meta']['Protocol']) {
                // The node is invalid, if the protocol is not equal with the client's protocol.
                continue;
            }

            foreach ($checks as $check) {
                $status = $check['Status'] ?? false;
                if ($status !== 'passing') {
                    $passing = false;
                }
            }

            if ($passing) {
                $address = $service['Address'] ?? '';
                $port = (int) $service['Port'] ?? 0;
                // @TODO Get and set the weight property.
                $address && $port && $nodes[] = new Node($address, $port);
            }
        }
        return $nodes;
    }

    public function getGroupName()
    {
        return $this->groupName;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    public function getPathGenerator()
    {
        return $this->pathGenerator;
    }
}
