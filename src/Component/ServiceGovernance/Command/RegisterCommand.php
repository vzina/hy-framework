<?php
/**
 * RegisterCommand.php
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
class RegisterCommand extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('service:register');
    }

    public function handle()
    {
        ApplicationContext::getContainer()->get(ServiceManager::class)->register();
    }
}
