<?php
/**
 * JsonRpcHttpPoolTransporter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc;

/**
 * description
 */
class JsonRpcHttpPoolTransporter extends JsonRpcHttpTransporter
{
    protected $clientOptions = [
        'timeout' => 5.0,
        'remove_disable_node' => false,
        'swoole' => [
            'keep_alive' => true,
        ],
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 32,
            'wait_timeout' => 3.0,
            'max_idle_time' => 60.0,
        ],
    ];
}
