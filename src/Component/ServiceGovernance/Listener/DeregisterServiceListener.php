<?php
/**
 * DeregisterServiceListener.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\ServiceGovernance\Listener;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Framework\Event\OnShutdown;
use Hyperf\Event\Contract\ListenerInterface;
use EyPhp\Framework\Component\ServiceGovernance\ServiceManager;

/**
 * description
 */
class DeregisterServiceListener implements ListenerInterface
{
    /**
     * @Inject
     * @var ServiceManager
     * @author weijian.ye
     */
    protected $serviceManager;

    public function listen(): array
    {
        return [OnShutdown::class];
    }

    public function process(object $event)
    {
        $this->serviceManager->deregister();
    }
}
