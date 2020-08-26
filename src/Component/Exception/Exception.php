<?php
/**
 * Exception.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Exception;

use Exception as GlobalException;
use EyPhp\Framework\Component\Exception\Contract\ExceptionInterface;
use EyPhp\Framework\Component\Message\StatusCode;

/**
 * description
 */
class Exception extends GlobalException implements ExceptionInterface
{
    protected $code = StatusCode::BAD_REQUEST;
}
