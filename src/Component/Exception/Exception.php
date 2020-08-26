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
use Hyperf\Utils\Codec\Json;

/**
 * description
 */
class Exception extends GlobalException implements ExceptionInterface
{

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'file' => $this->file,
            'line' => $this->line
        ];
    }

    public function toJson(): string
    {
        return Json::encode($this->toArray());
    }

    public function toXml(): string
    {
        // TODO: 未实现
        return '';
    }
}
