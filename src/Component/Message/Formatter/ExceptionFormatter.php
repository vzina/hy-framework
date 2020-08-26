<?php
/**
 * ExceptionFormatter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Message\Formatter;

use EyPhp\Framework\Component\Exception\Contract\ExceptionInterface;

/**
 * description
 */
class ExceptionFormatter extends StdoutFormatter
{
    /**
     * format
     * @param  ExceptionInterface $data
     * @return string
     */
    public function format($data): string
    {
        return (string)$data;
    }
}
