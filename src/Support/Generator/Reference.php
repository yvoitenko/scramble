<?php

namespace Dedoc\Scramble\Support\Generator;

use Dedoc\Scramble\Support\Generator\Combined\AnyOf;
use Dedoc\Scramble\Support\Generator\Types\NullType;
use Dedoc\Scramble\Support\Generator\Types\Type;

class Reference extends Type
{
    public string $referenceType;

    public string $fullName;

    private Components $components;

    public function __construct(string $referenceType, string $fullName, Components $components)
    {
        $this->referenceType = $referenceType;
        $this->fullName = $fullName;
        $this->components = $components;
    }

    public static function make(string $referenceType, string $fullName, Components $components): static
    {
        return new static(
            referenceType: $referenceType,
            fullName: $fullName,
            components: $components,
        );
    }

    public function resolve()
    {
        return $this->components->get($this);
    }

    public function getUniqueName()
    {
        return $this->components->uniqueSchemaName($this->fullName);
    }

    public function toArray()
    {
        if ($this->nullable) {
            return (new AnyOf)->setItems([(clone $this)->nullable(false), new NullType])->toArray();
        }

        return array_filter([
            'description' => $this->description,
            '$ref' => "#/components/{$this->referenceType}/{$this->getUniqueName()}",
        ]);
    }
}
