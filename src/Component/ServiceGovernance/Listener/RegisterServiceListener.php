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

use Hyperf\Event\Contract\ListenerInterface;

/**
 * description
 */
class RegisterServiceListener implements ListenerInterface
{

    public function listen(): array
    {
        return [];
    }

    public function process(object $event)
    {
        // code.
    }
}
