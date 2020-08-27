<?php
/**
 * Coroutine.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Utils;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine as UtilsCoroutine;
use Swoole\Coroutine as SwooleCoroutine;
use Throwable;

/**
 * description
 */
class Coroutine extends UtilsCoroutine
{
    /**
     * @return int Returns the coroutine ID of the coroutine just created.
     *             Returns -1 when coroutine create failed.
     */
    public static function create(callable $callable): int
    {
        $traceId = static::traceId(); // 添加协程id
        $result = SwooleCoroutine::create(function () use ($callable, $traceId) {
            try {
                static::traceId($traceId);
                call($callable);
            } catch (Throwable $throwable) {
                if (ApplicationContext::hasContainer()) {
                    $container = ApplicationContext::getContainer();
                    if ($container->has(StdoutLoggerInterface::class)) {
                        /* @var LoggerInterface $logger */
                        $logger = $container->get(StdoutLoggerInterface::class);
                        /* @var FormatterInterface $formatter */
                        if ($container->has(FormatterInterface::class)) {
                            $formatter = $container->get(FormatterInterface::class);
                            $logger->warning($formatter->format($throwable));
                        } else {
                            $logger->warning(sprintf('Uncaptured exception[%s] detected in %s::%d.', get_class($throwable), $throwable->getFile(), $throwable->getLine()));
                        }
                    }
                }
            }
        });
        return is_int($result) ? $result : -1;
    }

    public static function traceId(string $traceId = ''): string
    {
        return Context::override(__CLASS__ . '.trace_id', function ($oldTraceId) use ($traceId) {
            if (empty($traceId)) {
                return $oldTraceId ?: value(function () {
                    mt_srand(); // https://wiki.swoole.com/#/getting_started/notice?id=mt_rand%e9%9a%8f%e6%9c%ba%e6%95%b0
                    return md5(uniqid((string) mt_rand(), true));
                });
            }
            return $traceId;
        });
    }
}
