<?php
/**
 * RotatingFileHandler.php
 * PHP version 7
 *
 * @package hyperf-skeleton
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\Logger\Handler;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler as HandlerRotatingFileHandler;

/**
 * description
 */
class RotatingFileHandler extends HandlerRotatingFileHandler
{
    /**
     * @var int
     */
    protected $chunkSize = 8192;

    public function __construct($filename, $maxFiles = 0, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false, $chunkSize = 8192)
    {
        $this->chunkSize = $chunkSize;
        parent::__construct($filename, $maxFiles, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * Write to stream
     * @param resource $stream
     * @param array    $record
     */
    protected function streamWrite($stream, array $record): void
    {
        stream_set_chunk_size($stream, $this->chunkSize); // 大内容日志写入被分割问题
        parent::streamWrite($stream, $record);
    }
}
