<?php
/**
 * LoggerExceptionListener.php
 * PHP version 7
 *
 * @category hyperf-client
 * @author   Weijian.Ye <yeweijian@3k.com>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/vzina
 * @date     2020-08-26
 */
declare(strict_types=1);

namespace EyPhp\Framework\Component\Exception\Listener;


use EyPhp\Framework\Component\Exception\Event\ExceptionEvent;
use EyPhp\Framework\Component\Logger\SysLog;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class LoggerExceptionListener implements ListenerInterface
{
    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            ExceptionEvent::class,
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function process(object $event)
    {
        // todo: 错误日志处理
        if ($event instanceof ExceptionEvent) {
            $logger = SysLog::get(SysLog::ERROR);
            $throwable = $event->throwable;
            $logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        }
    }
}