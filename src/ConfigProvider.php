<?php
declare (strict_types = 1);

namespace EyPhp\Framework;

use EyPhp\Framework\Component\Exception\ExceptionHandler;
use EyPhp\Framework\Component\JsonRpc\Listeners\AutoAddConsumerDefinitionListener;
use EyPhp\Framework\Component\Logger\StdoutLoggerFactory;
use Hyperf\Contract\StdoutLoggerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => array_merge([
                // code ...
            ], value(function () {
                $result = [];
                if ((bool) env('LOGGER_STDOUT_FILE', false)) {
                    $result[StdoutLoggerInterface::class] = StdoutLoggerFactory::class;
                }
                return $result;
            })),
            'listeners' => array_merge([
                // code ...
            ], value(function () {
                $result = [];
                if ((bool) env('CONSUMERS_AUTO_SERVICES_ENABLE', false)) {
                    $result[] = AutoAddConsumerDefinitionListener::class;
                }
                return $result;
            })),
            'middlewares' => [
                'http' => [],
                'jsonrpc-http' => [],
                'jsonrpc-tcp-length-check' => [],
            ],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        ExceptionHandler::class
                    ],
                    'jsonrpc-http' => [
                        ExceptionHandler::class
                    ],
                    'jsonrpc-tcp-length-check' => [
                        ExceptionHandler::class
                    ],
                ],
                'code' => [],
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'collectors' => [],
                ],
            ],
            'publish' => [
                // [
                //     'id' => 'config',
                //     'description' => 'The config for framework.',
                //     'source' => __DIR__ . '/../publish/framework.php',
                //     'destination' => BASE_PATH . '/config/autoload/framework.php',
                // ],
                [
                    'id' => 'config',
                    'description' => 'The config for rpc router.',
                    'source' => __DIR__ . '/../publish/autoload/services.php',
                    'destination' => BASE_PATH . '/config/autoload/services.php',
                ],
                [
                    'id' => 'config',
                    'description' => 'The config for rpc router.',
                    'source' => __DIR__ . '/../publish/services.php',
                    'destination' => BASE_PATH . '/config/services.php',
                ],
            ],
            'commands' => [],
        ];
    }
}
