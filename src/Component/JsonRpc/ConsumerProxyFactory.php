<?php
/**
 * ProxyFactory.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc;

use EyPhp\Framework\Component\JsonRpc\ConsumerProxy\Ast;
use Hyperf\RpcClient\ProxyFactory;

/**
 * description
 */
class ConsumerProxyFactory extends ProxyFactory
{
    public function __construct()
    {
        parent::__construct();
        $this->ast = new Ast();
    }
}
