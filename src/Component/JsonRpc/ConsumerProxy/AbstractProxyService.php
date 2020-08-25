<?php
/**
 * AbstractProxyService.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc\ConsumerProxy;

use Psr\Container\ContainerInterface;
use EyPhp\Framework\Component\JsonRpc\ServiceClient;
use EyPhp\Framework\Component\JsonRpc\ProxyClient;
use Hyperf\RpcClient\Proxy\AbstractProxyService as BaseAbstractProxyService;

/**
 * description
 */
class AbstractProxyService extends BaseAbstractProxyService
{
    public function __construct(ContainerInterface $container, string $groupName, string $serviceName, string $protocol, array $options = [])
    {
        $this->client = make(ServiceClient::class, [
            'container' => $container,
            'groupName' => $groupName,
            'serviceName' => $serviceName,
            'protocol' => $protocol,
            'options' => $options,
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }
}
