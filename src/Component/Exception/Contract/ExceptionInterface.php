<?php
/**
 * ExceptionInterface.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Exception\Contract;

/**
 * description
 */
interface ExceptionInterface
{
    /**
     * toArray
     * @return array
     */
    public function toArray(): array;

    /**
     * toJson
     * @return string
     */
    public function toJson(): string;

    /**
     * toXml
     * @return string
     */
    public function toXml(): string;
}
