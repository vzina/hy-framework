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
     * @var string
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $proxyServiceName = '';

    /**
     * @var ProxyManager
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $proxyManager;

    public function __construct(ProxyManager $proxyManager)
    {
        $this->proxyManager = $proxyManager;
    }

    public function setProxyService(string $proxyServiceName)
    {
        $this->proxyServiceName = $proxyServiceName;
        return $this;
    }

    public function __call($method, $argv)
    {
        $this->proxyManager->setProxyData($this->proxyServiceName, $method, $argv);
        return $this;
    }
}
