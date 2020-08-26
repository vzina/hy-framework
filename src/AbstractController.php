<?php
/**
 * AbstractController.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework;

use EyPhp\Framework\Component\Message\ResultEntity;
use EyPhp\Framework\Component\Message\StatusCode;
use Psr\Container\ContainerInterface;

/**
 * description
 */
abstract class AbstractController
{
    /**
     * @var ResultEntity
     * @author weijian.ye <yeweijian299@163.com>
     */
    private $resultEntity;

    /**
     * @var ContainerInterface
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->resultEntity = make(ResultEntity::class);
    }

    protected function result($data = [], $code = StatusCode::OK, $message = '')
    {
        return $this->resultEntity->setCode($code)
            ->setMessage($message)
            ->setData($data);
    }
}
