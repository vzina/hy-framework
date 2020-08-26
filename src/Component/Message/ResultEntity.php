<?php
/**
 * ResultEntity.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\Message;

use Hyperf\Utils\Contracts\Arrayable;
use JsonSerializable;

/**
 * description
 */
class ResultEntity implements Arrayable, JsonSerializable
{
    protected $code;
    protected $message;
    protected $data;

    public function getCode(): int
    {
        return is_null($this->code) ? StatusCode::OK : intval($this->code);
    }

    public function setCode(int $code)
    {
        $this->code = $code;
        return $this;
    }

    public function getMessage()
    {
        return $this->message ?: StatusCode::getMessage($this->getCode());
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
        ];
    }
}
