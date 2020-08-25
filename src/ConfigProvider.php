<?php
declare (strict_types = 1);

namespace EyPhp\Framework;

use EyPhp\Framework\Component\JsonRpc\Listeners\AutoAddConsumerDefinitionListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [],
            'listeners' => [
                value(function() {
                    return env('CONSUMERS_AUTO_SERVICES_ENABLE', false) ? AutoAddConsumerDefinitionListener::class : null;
                })
            ],
            'middlewares' => [
                'http' => [],
                'jsonrpc-http' => [],
                'jsonrpc-tcp-length-check' => [],
            ],
            'exceptions' => [
                'handler' => [
                    'http' => [],
                    'jsonrpc-http' => [],
                    'jsonrpc-tcp-length-check' => [],
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
                    'source' => __DIR__ . '/../publish/services.php',
                    'destination' => BASE_PATH . '/config/services.php',
                ],
            ],
            'commands' => [],
        ];
    }
}
