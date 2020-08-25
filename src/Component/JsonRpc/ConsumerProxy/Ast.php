<?php
/**
 * Ast.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types=1);

namespace EyPhp\Framework\Component\JsonRpc\ConsumerProxy;

use PhpParser\NodeTraverser;
use Hyperf\RpcClient\Proxy\Ast as BaseAst;

/**
 * description
 */
class Ast extends BaseAst
{
    public function proxy(string $className, string $proxyClassName)
    {
        if (! interface_exists($className)) {
            throw new \InvalidArgumentException("'{$className}' should be an interface name");
        }
        if (strpos($proxyClassName, '\\') !== false) {
            $exploded = explode('\\', $proxyClassName);
            $proxyClassName = end($exploded);
        }

        $code = $this->codeLoader->getCodeByClassName($className);
        $stmts = $this->astParser->parse($code);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ProxyCallVisitor($proxyClassName));
        $modifiedStmts = $traverser->traverse($stmts);
        return $this->printer->prettyPrintFile($modifiedStmts);
    }
}
