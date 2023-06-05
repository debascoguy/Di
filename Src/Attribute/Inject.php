<?php

namespace Emma\Di\Attribute;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
use Attribute;
use InvalidArgumentException;


#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
final class Inject
{
    /**
     * @var string|null
     * Name of Injectable
     */
    private ?string $name = null;

    /**
     * Parameters, indexed by the parameter number (index) or name.
     * Used if the attribute is set on a method
     */
    private array $parameters = [];

    
    public function __construct(string|array|null $name = null)
    {
        if (is_string($name)) {
            $this->name = $name;
        }

         if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (!is_string($value)) {
                    throw new InvalidArgumentException(sprintf(
                        "#[Inject(['param' => 'value'])] expects 'value' to be a string, %s given.",
                        json_encode($value, \JSON_THROW_ON_ERROR)
                    ));
                }
                $this->parameters[$key] = $value;
            }
        }
    }

    /**
     * @return string|null
     */ 
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array
     */ 
    public function getParameters(): array
    {
        return $this->parameters;
    }
}