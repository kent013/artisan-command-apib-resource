<?php

declare(strict_types=1);

namespace ArtisanCommandApibResource;

use BenSampo\Enum\Enum;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;
use ReflectionNamedType;
use Webmozart\Assert\Assert;

class ApibResourceGenerateCommand extends Command
{
    /** @var string */
    protected $signature = 'generate:apib {class_path} {--file} {--output-directory=} {--stdout}';
    /**
     * @var string
     */
    protected $outputDirectory = 'tmp/apib/';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generatet apib resource markup';

    /**
     * @return bool|void|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        /** @var string */
        $classPath = $this->argument('class_path');
        /** @var class-string */
        $classPath = str_replace('/', '\\', $classPath);
        $class = new ReflectionClass($classPath);

        if($class->isSubclassOf(Enum::class)) {
            $this->generateMarkdownForEnum($classPath);
        } elseif($class->isSubclassOf(JsonResource::class)) {
            $this->generateMarkdownForJsonResource($classPath);
        }
    }

    /**
     * @param class-string $classPath
     */
    protected function generateMarkdownForEnum(string $classPath): void
    {
        $class = new ReflectionClass($classPath);
        $values = $classPath::getValues();
        $type = gettype($values[0]);
        $values = implode("\n", array_map(function ($value) {
            /** @var string|int */
            $aValue = $value;
            return "- {$aValue}";
        }, $values));
        $markdown = "## {$class->getShortName()} (enum[{$type}])\n### Items\n{$values}";
        $this->generateMarkdown($class->getShortName(), $markdown);
    }

    /**
     * @param class-string $classPath
     */
    protected function generateMarkdownForJsonResource(string $classPath): void
    {
        $class = new ReflectionClass($classPath);
        $docComment = $class->getDocComment();

        if(!$docComment || preg_match('/@mixin ([\w\\\]+)/', $docComment, $matches) === 0) {
            $this->error('mixin not found');
            return;
        }

        $mixInClassName = $matches[1];
        $mixInClass = $this->getMixInClass($mixInClassName, $class);

        if(!$mixInClass) {
            $this->error('mixin class not found');
            return;
        }

        if(!$mixInClass->hasMethod('factory')) {
            $this->error('factory method not found');
            return;
        }

        $factoryMethod = $mixInClass->getMethod('factory');
        /** @var Factory<Model> */
        $factory = $factoryMethod->invoke(null);

        /** @var Model */
        $model = $factory->make();

        // @phpstan-ignore-next-line
        $model->id = $model->id ?? 123;
        // @phpstan-ignore-next-line
        $model->created_at = $model->created_at ?? now();
        // @phpstan-ignore-next-line
        $model->updated_at = $model->updated_at ?? now();

        /** @var JsonResource */
        $resource = $class->newInstance($model);
        $values = $this->getValuesFromResource($resource, $model);

        Assert::isArray($values);

        $resourceShortName = $this->getResourceShortName($classPath);
        $markdown = "## {$resourceShortName} (object)\n";

        foreach($values as $key => $value) {
            $type = gettype($value);
            $modelValue = $value;

            try {
                if(isset($model->{$key})) {
                    $type = gettype($model->{$key});
                    $modelValue = $model->{$key};
                }
            } catch(Exception $e) {
            }

            if($type === 'object') {
                $className = get_class($modelValue);

                if(!$className) {
                    throw new Exception('class name not found');
                }
                $reflection = new ReflectionClass($className);
                $type = $reflection->getShortName();
            }

            $type = match($type) {
                'integer' => $type = 'number',
                default => $type
            };

            if(gettype($value) === 'object') {
                if(str_ends_with($type, 'Resource')) {
                    $type = $this->getResourceShortName($type);
                    $markdown .= "+ `{$key}` ({$type}, required)\n";
                } elseif(str_ends_with($type, 'Collection')) {
                    $collectionType = $this->getCollectionAttributeTypeFromSourceCode($resource, $key);

                    if($collectionType) {
                        $markdown .= "+ `{$key}` (array, required, fixed-type)\n    + ({$collectionType})\n";
                    } else {
                        $markdown .= "+ `{$key}` (array, required)\n";
                    }
                } else {
                    $markdown .= "+ `{$key}` ({$type}, required)\n";
                }
            } else {
                $markdown .= "+ `{$key}`: {$value} ({$type}, required)\n";
            }
        }
        $this->generateMarkdown($class->getShortName(), $markdown);
    }

    /**
     * @param JsonResource $resource
     * @param string $atributeName
     * @return string|null
     */
    protected function getCollectionAttributeTypeFromSourceCode(JsonResource $resource, string $atributeName): string|null
    {
        $resourceClass = new ReflectionClass($resource);

        if(!$resourceClass->getFileName()) {
            return null;
        }
        $resourceClassFile = file_get_contents($resourceClass->getFileName());

        if($resourceClassFile && preg_match("/'{$atributeName}'\\s*=>\\s*(.+?)Resource\\s*::\\s*collection/", $resourceClassFile, $matches) === 1) {
            return $this->getResourceShortName($matches[1]);
        }
        return null;
    }

    /**
     * @param JsonResource $resource
     * @param Model $model
     * @return array<mixed>
     */
    protected function getValuesFromResource(JsonResource $resource, Model $model): array
    {
        try {
            /** @var array<mixed> */
            $values = $resource->toArray(request());
            return $values;
        } catch(Exception $e) {
        }

        $modelClass = new ReflectionClass($model);
        $resourceClass = new ReflectionClass($resource);

        $methods = $modelClass->getMethods();
        $relationMethodNames = [];

        foreach($methods as $method) {
            $returnType = $method->getReturnType();

            if(!($returnType instanceof ReflectionNamedType)) {
                continue;
            }

            if(str_starts_with($returnType->getName(), 'Illuminate\\Database\\Eloquent\\Relations')) {
                $relationMethodNames[] = $method->name;
            }
        }

        if(!$resourceClass->getFileName()) {
            return [];
        }

        $resourceClassFile = file_get_contents($resourceClass->getFileName());

        if(!$resourceClassFile) {
            return [];
        }
        $resourceClassFile = preg_replace_callback('/\$this->(\w+)/', function ($matches) use ($relationMethodNames) {
            if(in_array($matches[1], $relationMethodNames)) {
                return '[]';
            }
            return $matches[0];
        }, $resourceClassFile);

        if(!$resourceClassFile) {
            return [];
        }
        $resourceClassShortName = $resourceClass->getShortName();
        $resourceEvalClassShortName = "{$resourceClassShortName}Eval";
        $resourceClassFile = str_replace("class {$resourceClassShortName}", "class {$resourceEvalClassShortName}", $resourceClassFile);
        $resourceClassFile = preg_replace('/^<\?php.+/', '', $resourceClassFile);
        $resourceClassFile .= "\nreturn new {$resourceEvalClassShortName}(\$model);";
        $evalResource = eval($resourceClassFile);

        return $evalResource->toArray(request());
    }

    protected function getResourceShortName(string $resourceClassName): string
    {
        if(preg_match('/([\w]+)Resource$/', $resourceClassName, $matches) === 1) {
            return $matches[1];
        }
        return $resourceClassName;
    }

    /**
     * @param string $mixInClassName
     * @param ReflectionClass<object> $class
     * @return ReflectionClass<object>|null
     */
    protected function getMixInClass(string $mixInClassName, ReflectionClass $class): ReflectionClass|null
    {
        if(strpos($mixInClassName, '\\') !== false) {
            /** @var class-string $mixInClassName */
            return new ReflectionClass($mixInClassName);
        }
        $filename = $class->getFileName();

        if(!$filename) {
            $this->error('file not found');
            return null;
        }
        $classFile = file_get_contents($filename);

        if(!$classFile || preg_match_all('/^use ([\w\\\]+);$/m', $classFile, $matches) === 0) {
            $this->error('file not found');
            return null;
        }
        /** @var array<string> */
        $classFQNs = $matches[1];

        /** @var class-string $fqn */
        foreach($classFQNs as $fqn) {
            if(str_ends_with($fqn, $mixInClassName)) {
                return new ReflectionClass($fqn);
            }
        }
        $this->error("class FQN form {$mixInClassName} not found");
        return null;
    }

    protected function generateMarkdown(string $classShortName, string $markdown): void
    {
        if($this->option('stdout')) {
            echo $markdown . "\n";
        }

        if($this->option('file') === false) {
            return;
        }
        $filename = "{$classShortName}.md";

        if($this->option('output-directory') !== null) {
            /** @var string */
            $outputDirectory = $this->option('output-directory');
            $this->outputDirectory = $outputDirectory;
        }
        $directory = base_path(($this->outputDirectory));

        if(!file_exists($directory)) {
            mkdir($directory);
        }
        file_put_contents("{$directory}/{$filename}", $markdown);
    }
}
