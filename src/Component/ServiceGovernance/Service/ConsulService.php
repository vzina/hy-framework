<?php
/**
 * ConsulAgent.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\ServiceGovernance\Adapter;

use EyPhp\Framework\Component\ServiceGovernance\Contract\DeregisterInterface;
use EyPhp\Framework\Component\ServiceGovernance\Contract\RegisterInterface;

/**
 * description
 */
class ConsulService implements RegisterInterface, DeregisterInterface
{

    public function register()
    {
        // code.
    }

    public function deregister()
    {
        // code.
    }
}
