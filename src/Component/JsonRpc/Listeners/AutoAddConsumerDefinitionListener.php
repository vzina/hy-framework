<?php
/**
 * AutoAddConsumerDefinitionListener.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc\Listeners;

use EyPhp\Framework\Component\JsonRpc\ConsumerProxyFactory;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Psr\Container\ContainerInterface;

/**
 * description
 */
class AutoAddConsumerDefinitionListener implements ListenerInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    /**
     * Automatic create proxy service definitions from services.consumers.
     *
     * @param BootApplication $event
     */
    public function process(object $event)
    {
        /** @var Container $container */
        $container = $this->container;
        if ($container instanceof Container) {
            $consumers = $container->get(ConfigInterface::class)->get('services.consumers', []);
            $serviceFactory = $container->get(ConsumerProxyFactory::class);
            $definitions = $container->getDefinitionSource();
            foreach ($consumers as $consumer) {
                if (empty($consumer['name']) || empty($consumer['auto_services'])) {
                    continue;
                }
                $serviceInterfaces = $consumer['auto_services'];
                foreach ($serviceInterfaces as $serviceInterface) {
                    if (!interface_exists($serviceInterface)) {
                        continue;
                    }

                    $proxyClass = $serviceFactory->createProxy($serviceInterface);
                    $definitions->addDefinition(
                        $serviceInterface,
                        function (ContainerInterface $container) use ($consumer, $serviceInterface, $proxyClass) {
                            return new $proxyClass(
                                $container,
                                $consumer['name'],
                                $this->getServiceName($serviceInterface),
                                $consumer['protocol'] ?? 'jsonrpc-http',
                                [
                                    'load_balancer' => $consumer['load_balancer'] ?? 'random',
                                    'service_interface' => $serviceInterface,
                                ]
                            );
                        }
                    );
                }
            }
        }
    }

    /**
     * 解析服务名称
     *
     * @param string $serviceInterface
     * @return string
     * @author weijian.ye <yeweijian299@163.com>
     */
    protected function getServiceName(string $serviceInterface): string
    {
        return substr((string) strrchr($serviceInterface, '\\'), 1, -9); // 截取服务名：UserServiceInterface => UserService
    }
}
