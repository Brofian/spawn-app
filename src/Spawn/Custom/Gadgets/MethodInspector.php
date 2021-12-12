<?php

namespace spawnCore\Custom\Gadgets;

use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use spawnCore\Custom\FoundationStorage\Mutable;
use spawnCore\Custom\Throwables\FailedConvertToReflectionObjectException;

class MethodInspector extends Mutable
{

    protected string $class;
    protected string $methodName;
    protected array $tags;
    protected bool $isStatic;
    protected bool $isAbstract;
    protected bool $isFinal;
    protected bool $isPublic;
    protected bool $isProtected;
    protected bool $isPrivate;
    protected array $parameters;


    /**
     * MethodInspector constructor.
     * @param string $class
     * @param string|ReflectionMethod $method
     * @throws ReflectionException
     * @throws FailedConvertToReflectionObjectException
     */
    public function __construct(string $class, $method)
    {
        $this->class = $class;
        if (is_string($method)) {
            $this->loadMethodData(new ReflectionMethod($this->class, $method));
        } elseif ($method instanceof ReflectionMethod) {
            $this->loadMethodData($method);
        } else {
            throw new FailedConvertToReflectionObjectException($class, $method);
        }
    }

    protected function loadMethodData(ReflectionMethod $reflectionMethod): void
    {
        $this->methodName = $reflectionMethod->getName();
        $this->isStatic = $reflectionMethod->isStatic();
        $this->isAbstract = $reflectionMethod->isAbstract();
        $this->isFinal = $reflectionMethod->isFinal();
        $this->isPublic = $reflectionMethod->isPublic();
        $this->isProtected = $reflectionMethod->isProtected();
        $this->isPrivate = $reflectionMethod->isPrivate();

        $this->parameters = $this->interpretParameters($reflectionMethod->getParameters() ?? []);
        $this->tags = $this->interpretPhpDoc($reflectionMethod->getDocComment() ?? '');
    }

    protected function interpretParameters(array $reflectionParameters): array
    {
        $parameters = [];

        /** @var ReflectionParameter $reflectionParameter */
        foreach ($reflectionParameters as $key => $reflectionParameter) {

            $parameters[$reflectionParameter->getPosition()] = [
                'name' => $reflectionParameter->getName(),
                'required' => !$reflectionParameter->isOptional()
            ];
        }

        return $parameters;
    }

    protected function interpretPhpDoc(string $phpDoc): array
    {
        $lines = explode(PHP_EOL, $phpDoc);
        //trim spaces, asterisk and slashes
        $lines = array_map(function ($line) {
            return trim($line, ' */');
        }, $lines);
        //remove now empty lines
        $lines = array_filter($lines);

        $docData = [];

        foreach ($lines as $line) {
            if (substr($line, 0, 1) !== '@') {
                continue;
            }
            $line = ltrim($line, '@');
            $elements = preg_split('/ /m', $line, -1, PREG_SPLIT_NO_EMPTY);
            $first = array_shift($elements);

            switch ($first) {
                case 'param':
                    if (count($elements) > 1) $docData['param'][$elements[1]] = $elements[0];
                    else $docData['param'][$elements[0]] = 'mixed';
                    break;
                case 'throws':
                    $docData['throws'] = $elements[0];
                    break;
                case 'return':
                    $docData['return'] = $elements[0];
                    break;
                case 'route':
                    $docData['route'] = $elements[0];
                    break;
                case 'locked':
                    $docData['locked'] = true;
                    break;
                default:
                    $docData[$first][] = $elements;
                    break;
            }
        }

        return $docData;
    }


    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string $tag
     * @param mixed $fallback
     * @return mixed
     */
    public function getTag(string $tag, $fallback = null)
    {
        return $this->tags[$tag] ?? $fallback;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function isAbstract(): bool
    {
        return $this->isAbstract;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function isProtected(): bool
    {
        return $this->isProtected;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }


}