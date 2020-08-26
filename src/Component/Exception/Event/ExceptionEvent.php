<?php
/**
 * ExceptionEvent.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Exception\Event;

use Throwable;

/**
 * description
 */
class ExceptionEvent
{
    /**
     * 异常信息
     * @var Throwable
     */
    public $throwable;

    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }
}
