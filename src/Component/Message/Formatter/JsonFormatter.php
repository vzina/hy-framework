<?php
/**
 * JsonFormatter.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Message\Formatter;

use Hyperf\Utils\Codec\Json;

/**
 * description
 */
class JsonFormatter extends StdoutFormatter
{
    public function format($data): string
    {
        return parent::format(Json::encode($data));
    }
}
