<?php
/**
 * SysLogger.php
 * PHP version 7
 *
 * @category hyperf-client
 * @author   Weijian.Ye <yeweijian@3k.com>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/vzina
 * @date     2020-08-26
 */
declare(strict_types=1);

namespace EyPhp\Framework\Component\Logger;


use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class SysLog
{
    const DEFAULT = 'default';
    const ERROR = 'error';
    const TRACE = 'trace';
    const SQL = 'sql';

    public static function get(string $name = self::DEFAULT): Logger
    {
        return ApplicationContext::getContainer()
            ->get(LoggerFactory::class)
            ->get('SysLog', $name);
    }
}