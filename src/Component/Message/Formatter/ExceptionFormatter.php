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

use EyPhp\Framework\Component\Exception\Exception;
use EyPhp\Framework\Component\Message\ResultEntity;
use EyPhp\Framework\Component\Message\StatusCode;

/**
 * description
 */
class ExceptionFormatter extends StdoutFormatter
{
    /**
     * format
     * @param  Exception $exception
     * @return string
     */
    public function format($exception): string
    {
        $resultEntity = make(ResultEntity::class);
        if ($exception->getPrevious()) {
            $exception = $exception->getPrevious();
            $resultEntity->setCode(StatusCode::INTERNAL_SERVER_ERROR);
        } else {
            $resultEntity->setCode($exception->getCode())
                ->setMessage($exception->getMessage());
        }
        return (string)$resultEntity;
    }
}
