<?php
/**
 * DbQueryExecutedListener.php
 * PHP version 7
 *
 * @category hyperf-client
 * @author   Weijian.Ye <yeweijian@3k.com>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/vzina
 * @date     2020-08-26
 */
declare(strict_types=1);

namespace EyPhp\Framework\Listener;

use EyPhp\Framework\Component\Logger\SysLog;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;

class DbQueryExecutedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event)
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            if (! Arr::isAssoc($event->bindings)) {
                foreach ($event->bindings as $key => $value) {
                    $sql = Str::replaceFirst('?', "'{$value}'", $sql);
                }
            }

            SysLog::get(SysLog::SQL)->info(sprintf('[%s] %s', $event->time, $sql));
        }
    }
}