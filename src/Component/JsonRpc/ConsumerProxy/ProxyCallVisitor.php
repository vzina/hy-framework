<?php
/**
 * ProxyCallVisitor.php
 * PHP version 7
 *
 * @package framework
 * @author  weijian.ye
 * @contact yeweijian299@163.com
 * @link    https://github.com/vzina
 */
declare (strict_types = 1);

namespace EyPhp\Framework\Component\JsonRpc\ConsumerProxy;

use PhpParser\Node;
use ReflectionMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\Interface_;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * description
 */
class ProxyCallVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $classname;

    /**
     * @var string
     */
    private $namespace;

    public function __construct(string $classname)
    {
        $this->classname = $classname;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node->name->toCodeString();
        }
        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Interface_) {
            $node->stmts = $this->generateStmts($node);
            return new Node\Stmt\Class_($this->classname, [
                'stmts' => $node->stmts,
                'extends' => new Node\Name\FullyQualified(AbstractProxyService::class),
                'implements' => [
                    $node->name,
                ],
            ]);
        }
        return null;
    }

    public function generateStmts(Interface_ $node): array
    {
        $betterReflectionInterface = ReflectionClass::createFromName($this->namespace . '\\' . $node->name);
        $reflectionMethods = $betterReflectionInterface->getMethods(ReflectionMethod::IS_PUBLIC);
        $stmts = [];
        foreach ($reflectionMethods as $method) {
            $stmts[] = $this->overrideMethod($method->getAst());
        }
        return $stmts;
    }

    protected function overrideMethod(Node\FunctionLike $stmt): Node\Stmt\ClassMethod
    {
        if (!$stmt instanceof Node\Stmt\ClassMethod) {
            throw new \InvalidArgumentException('stmt must instanceof Node\Stmt\ClassMethod');
        }
        $stmt->stmts = value(function () use ($stmt) {
            $methodCall = new Node\Expr\MethodCall(
                new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), new Node\Identifier('client')),
                new Node\Identifier('__call'),
                [
                    new Node\Arg(new Node\Scalar\MagicConst\Function_()),
                    new Node\Arg(new Node\Expr\FuncCall(new Node\Name('func_get_args'))),
                ]
            );
            if ($this->shouldReturn($stmt)) {
                return [new Node\Stmt\Return_($methodCall)];
            }
            return [new Node\Stmt\Expression($methodCall)];
        });
        return $stmt;
    }

    protected function shouldReturn(Node\Stmt\ClassMethod $stmt): bool
    {
        return $stmt->getReturnType() instanceof Node\NullableType
        || $stmt->getReturnType() instanceof Node\UnionType
            || ((string) $stmt->getReturnType()) !== 'void';
    }
}
