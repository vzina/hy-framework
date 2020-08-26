<?php
/**
 * ResponseFormatterInterface.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Message\Contract;

/**
 * description
 */
interface ResponseFormatterInterface
{
    public function format($data): string;
}
