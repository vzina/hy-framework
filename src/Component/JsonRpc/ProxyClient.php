<?php
/**
 * ServiceProxyClient.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc;

/**
 * description
 */
class ProxyClient
{
    /**
     * @var ServiceClient
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $client;
    protected $data = [];
    protected $serviceName = 'RpcProxyService';

    public function __construct(ServiceClient $client)
    {
        $this->client = $client;
    }

    public function __call($method, $argv)
    {
        $service = $this->client->getServiceName();
        $func = $this->client->getPathGenerator()->generate($service, $method);
        $this->data[] = [$func, $argv];
    }

    public function wait()
    {
        return $this->client->setServiceName($this->serviceName)->__call(__FUNCTION__, $this->data);
    }
}
