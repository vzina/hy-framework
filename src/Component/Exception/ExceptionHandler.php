<?php
/**
 * ExceptionHandler.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Exception;

use EyPhp\Framework\Component\Exception\Contract\ExceptionInterface;
use Throwable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use EyPhp\Framework\Component\Exception\Event\ExceptionEvent;
use EyPhp\Framework\Component\Message\Formatter\ExceptionFormatter;
use EyPhp\Framework\Component\Message\ResponseFormatterFactory;
use Hyperf\ExceptionHandler\ExceptionHandler as HyperfExceptionHandler;

/**
 * description
 */
class ExceptionHandler extends HyperfExceptionHandler
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->container->get(EventDispatcherInterface::class)->dispatch(new ExceptionEvent($throwable));
        return ResponseFormatterFactory::format(
            $response,
            $this->toException($throwable),
            ExceptionFormatter::class
        );
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    protected function toException(Throwable $throwable)
    {
        if ($throwable instanceof ExceptionInterface) {
            return $throwable;
        }

        return new Exception($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
