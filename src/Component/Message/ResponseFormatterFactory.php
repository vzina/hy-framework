<?php
/**
 * ResponseFormatterFactory.php
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
use EyPhp\Framework\Component\Message\Formatter\StdoutFormatter;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;

/**
 * description
 */
class ResponseFormatterFactory
{
    public static function format(ResponseInterface $response, $data, string $type = StdoutFormatter::class): ResponseInterface
    {
        $formatter = ApplicationContext::getContainer()->get($type);
        if (!($formatter instanceof ResponseFormatterInterface)) {
            $formatter = ApplicationContext::getContainer()->get(StdoutFormatter::class);
        }

        return $response->withBody(new SwooleStream($formatter->format($data)));
    }
}
