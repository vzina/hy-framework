<?php
/**
 * StdoutFormatter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Message\Formatter;

use EyPhp\Framework\Component\Message\StatusCode;
use EyPhp\Framework\Component\Message\Contract\ResponseFormatterInterface;

/**
 * description
 */
class StdoutFormatter implements ResponseFormatterInterface
{

    public function statusCode(): int
    {
        return StatusCode::OK;
    }

    public function format($data): string
    {
        return $data;
    }
}
