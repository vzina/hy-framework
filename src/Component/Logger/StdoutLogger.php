<?php
/**
 * StdoutLogger.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Logger;

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

/**
 * description
 */
class StdoutLogger
{
    public function __invoke(ContainerInterface $container)
    {
        return SysLog::get();
    }
}
