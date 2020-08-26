<?php
/**
 * JsonRpcHttpTransporter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc;

use EyPhp\Framework\Component\Guzzle\CoroutineHandler;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\LoadBalancer\LoadBalancerInterface;
use Hyperf\LoadBalancer\Node;
use Hyperf\Rpc\Contract\TransporterInterface;
use Hyperf\Utils\Coroutine;

/**
 * description
 */
class JsonRpcHttpTransporter implements TransporterInterface
{
    /**
     * @var LoadBalancerInterface
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $loadBalancer;

    /**
     * If $loadBalancer is null, will select a node in $nodes to request,
     * otherwise, use the nodes in $loadBalancer.
     *
     * @var Node[]
     */
    protected $nodes = [];

    /**
     * @var float
     */
    protected $connectTimeout = 5;

    /**
     * @var float
     */
    protected $recvTimeout = 5;

    /**
     * @var integer
     */
    protected $retryTimes = 2;

    /**
     * @var array
     */
    protected $clientOptions = [
        'timeout' => 5.0,
        'remove_disable_node' => false,
    ];

    public function __construct(array $config = [])
    {
        if (!isset($config['recv_timeout'])) {
            $config['recv_timeout'] = $this->recvTimeout;
        }
        if (!isset($config['connect_timeout'])) {
            $config['connect_timeout'] = $this->connectTimeout;
        }
        if (isset($config['retry_times'])) {
            $this->retryTimes = (int)$config['retry_times'];
            unset($config['retry_times']);
        }
        $this->clientOptions = array_merge($this->clientOptions, $config);
    }

    public function send(string $data)
    {
        return retry($this->retryTimes, function () use ($data) {
            $node = $this->getNode();
            $uri = $node->host . ':' . $node->port;
            $schema = value(function () use ($node) {
                $schema = 'http';
                if (property_exists($node, 'schema')) {
                    $schema = $node->schema;
                }
                if (!in_array($schema, ['http', 'https'])) {
                    $schema = 'http';
                }
                $schema .= '://';
                return $schema;
            });
            $url = $schema . $uri;
            $response = $this->getClient()->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'http_errors' => false,
                'body' => $data,
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->getBody()->getContents();
            }

            // 如果前面已经使用了ulb, lvs等代理， 则不要开启节点剔除功能
            if (!empty($this->clientOptions['remove_disable_node'])) {
                $this->getLoadBalancer()->removeNode($node);
            }

            return '';
        });
    }

    public function recv()
    {
        throw new \RuntimeException(__CLASS__ . ' does not support recv method.');
    }

    public function getClient(): Client
    {
        $options = $this->clientOptions;
        // Swoole HTTP Client cannot set recv_timeout and connect_timeout options, use timeout.
        $options['timeout'] = $options['recv_timeout'] + $options['connect_timeout'];
        unset($options['recv_timeout'], $options['connect_timeout']);

        $stack = null;
        if (Coroutine::getCid() > 0) {
            $stack = HandlerStack::create(new CoroutineHandler());
        }

        $config = array_replace(['handler' => $stack], $options);

        return make(Client::class, ['config' => $config]);
    }

    public function getLoadBalancer(): ?LoadBalancerInterface
    {
        return $this->loadBalancer;
    }

    public function setLoadBalancer(LoadBalancerInterface $loadBalancer): TransporterInterface
    {
        $this->loadBalancer = $loadBalancer;
        return $this;
    }

    /**
     * @param \Hyperf\LoadBalancer\Node[] $nodes
     */
    public function setNodes(array $nodes): self
    {
        $this->nodes = $nodes;
        return $this;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function getClientOptions(): array
    {
        return $this->clientOptions;
    }

    private function getEof()
    {
        return "\r\n";
    }

    /**
     * If the load balancer is exists, then the node will select by the load balancer,
     * otherwise will get a random node.
     */
    private function getNode(): Node
    {
        if ($this->loadBalancer instanceof LoadBalancerInterface) {
            return $this->loadBalancer->select();
        }
        return $this->nodes[array_rand($this->nodes)];
    }
}
