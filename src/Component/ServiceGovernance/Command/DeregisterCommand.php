<?php
/**
 * DeregisterCommand.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\ServiceGovernance\Command;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use EyPhp\Framework\Component\ServiceGovernance\ServiceManager;

/**
 * description
 * @Command
 */
class DeregisterCommand extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('service:deregister');
    }

    public function handle()
    {
        ApplicationContext::getContainer()->get(ServiceManager::class)->deregister();
        var_dump(11111);
    }
}
