<?php
/**
 * CoroutineHandler.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Guzzle;

use GuzzleHttp\Promise\FulfilledPromise;
use Hyperf\Guzzle\CoroutineHandler as HyperfGuzzleCoroutineHandler;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Psr\Http\Message\RequestInterface;
use Swoole\Coroutine\Http\Client;

/**
 * description
 */
class CoroutineHandler extends HyperfGuzzleCoroutineHandler
{
    /**
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $uri = $request->getUri();
        $host = $uri->getHost();
        $port = $uri->getPort();
        $scheme = $uri->getScheme();
        $ssl = $scheme === 'https';
        $path = $uri->getPath();
        $query = $uri->getQuery();

        if (empty($port)) {
            $port = $ssl ? 443 : 80;
        }
        if (empty($path)) {
            $path = '/';
        }
        if ($query !== '') {
            $path .= '?' . $query;
        }

        /** @var Client $client */
        $client = $this->getConnection($scheme, $host, $port, $ssl);
        $client->setMethod($request->getMethod());
        $client->setData((string) $request->getBody());

        // 初始化Headers
        $this->initHeaders($client, $request, $options);
        // 初始化配置
        $settings = $this->getSettings($request, $options);
        // 设置客户端参数
        if (!empty($settings)) {
            $client->set($settings);
        }

        $ms = microtime(true);

        $this->execute($client, $path);

        $ex = $this->checkStatusCode($client, $request);
        if ($ex !== true) {
            $client->close(); // 关闭异常链接
            return \GuzzleHttp\Promise\rejection_for($ex);
        }

        $response = $this->getResponse($client, $request, $options, microtime(true) - $ms);

        return new FulfilledPromise($response);
    }

    protected function getConnection(string $scheme, string $host, int $port, bool $ssl): Client
    {
        return Context::getOrSet($this->getContextName($scheme, $host, $port), function () use ($host, $port, $ssl) {
            return new Client($host, $port, $ssl);
        });
    }

    protected function getContextName(string $scheme, string $host, int $port): string
    {
        return sprintf('guzzle.handler.%s.%s.%d', $scheme, $host, $port);
    }
}
