<?php
/**
 * RegisterServiceListener.php
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
use EyPhp\Framework\Utils\Coroutine;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\Event\Contract\ListenerInterface;
use EyPhp\Framework\Component\ServiceGovernance\ServiceManager;

/**
 * description
 */
class RegisterServiceListener implements ListenerInterface
{
    /**
     * @Inject
     * @var ServiceManager
     * @author weijian.ye
     */
    protected $serviceManager;

    public function listen(): array
    {
        return [MainWorkerStart::class];
    }

    public function process(object $event)
    {
        Coroutine::create(function(){
            Coroutine::sleep(5); // 延迟5s向注册中心注册， 避免服务尚未初始化完成
            $this->serviceManager->register();
        });
    }
}
