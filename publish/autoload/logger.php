<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    \EyPhp\Framework\Component\Logger\SysLog::DEFAULT => [
        'handler' => [
            'class' => \EyPhp\Framework\Component\Logger\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/info.log',
                'level' => Monolog\Logger::DEBUG,
                'chunkSize' => 8 * 1024 // 8k
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    \EyPhp\Framework\Component\Logger\SysLog::ERROR => [
        'handler' => [
            'class' => \EyPhp\Framework\Component\Logger\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/error.log',
                'level' => Monolog\Logger::WARNING,
                'chunkSize' => 8 * 1024 // 8k
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    \EyPhp\Framework\Component\Logger\SysLog::TRACE => [
        'handler' => [
            'class' => \EyPhp\Framework\Component\Logger\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/trace.log',
                'level' => Monolog\Logger::DEBUG,
                'chunkSize' => 8 * 1024 // 8k
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    \EyPhp\Framework\Component\Logger\SysLog::SQL => [
        'handler' => [
            'class' => \EyPhp\Framework\Component\Logger\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/sql.log',
                'level' => Monolog\Logger::INFO,
                'chunkSize' => 8 * 1024 // 8k
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
