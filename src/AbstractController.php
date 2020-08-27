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
use Hyperf\Di\Annotation\Inject;

/**
 * description
 */
abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * api 统一响应内容
     *
     * @param array $data
     * @param int $code
     * @param string $message
     * @return ResultEntity
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected function result($data = [], $code = StatusCode::OK, $message = ''): ResultEntity
    {
        return make(ResultEntity::class)->setCode($code)
            ->setMessage($message)
            ->setData($data);
    }
}
