<?php
/**
 * ResponseFactory.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Message;

use EyPhp\Framework\Component\Message\Contract\ResponseFormatterInterface;
use EyPhp\Framework\Component\Message\Formatter\JsonFormatter;
use EyPhp\Framework\Component\Message\Formatter\StdoutFormatter;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * description
 */
class ResponseFactory
{
    public static function format($data, ?ResponseInterface $response = null, string $type = StdoutFormatter::class): ResponseInterface
    {
        if ($data instanceof RequestInterface) {
            [$data, $type] = static::parseRequestData($data, $response);
        }
        $container = ApplicationContext::getContainer();
        $formatter = $container->get($type);
        if (!($formatter instanceof ResponseFormatterInterface)) {
            $formatter = $container->get(StdoutFormatter::class);
        }
        $response = $response ?: $container->get(ResponseInterface::class);

        /** @var ResponseFormatterInterface $formatter */
        return $response->withStatus($formatter->statusCode())
            ->withBody(new SwooleStream($formatter->format($data)));
    }

    protected static function parseRequestData(RequestInterface $request, ResponseInterface $response): array
    {
        $contentType = $request->getHeaderLine('content-type');
        $type = StdoutFormatter::class;
        switch (true) {
            case stripos($contentType, 'xml') !== false:
                $type = XmlFormatter::class;
                break;
        }

        return [$response->getBody()->getContents(), $type];
    }
}
