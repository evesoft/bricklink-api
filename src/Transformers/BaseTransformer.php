<?php

namespace Davesweb\BrinklinkApi\Transformers;

use DateTime;
use Jawira\CaseConverter\Convert;

class BaseTransformer
{
    protected string $dto;

    protected array $mapping = [];

    public function toObject(array $data): object
    {
        $values = [];

        foreach ($data as $key => $value) {
            $property = $this->mapping[$key] ?? (string) (new Convert($key))->toCamel();

            if (is_array($property)) {
                $listTransformer = $property[2] ?? null;
                $transformer     = $property[1];
                $property        = $property[0];

                if ('datetime' === $transformer) {
                    $values[$property] = new DateTime($value);
                } elseif ('array' === $transformer) {
                    if (null === $listTransformer) {
                        $values[$property] = (array) $value;
                    } else {
                        $values[$property] = [];
                        foreach ($value as $subValue) {
                            $values[$property][] = call_user_func([$this->getTransformerObject($listTransformer), 'toObject'], $subValue);
                        }
                    }
                } else {
                    $values[$property] = call_user_func([$this->getTransformerObject($transformer), 'toObject'], $value ?? []);
                }
            } else {
                $values[$property] = $value;
            }
        }

        return new ($this->dto)(...$values);
    }

    public function toArray(object $object): array
    {
        $values = [];

        $data = get_object_vars($object);

        foreach ($data as $property => $value) {
            $key = $this->findArrayKeyInMapping($property);

            if (isset($this->mapping[$key]) && is_array($this->mapping[$key])) {
                $transformer = $this->mapping[$key][1];
                if ('datetime' === $transformer) {
                    $values[$key] = $value instanceof DateTime ? $value->format('Y-m-d\TH:i:s\.\0\0\0\Z') : $value;
                } else {
                    $values[$key] = null !== $value ? call_user_func([$this->getTransformerObject($transformer), 'toArray'], $value) : null;
                }
            } else {
                $values[$key] = $value;
            }
        }

        // Filter out null properties
        $values = array_filter($values, function ($value) {
            return null !== $value;
        });

        return $values;
    }
    protected function getTransformerObject(string $transformer): BaseTransformer
    {
        return new $transformer();
    }

    protected function findArrayKeyInMapping(string $property): string
    {
        $key = array_search($property, $this->mapping, true);

        if (false !== $key) {
            return $key;
        }

        foreach ($this->mapping as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            if ($value[0] === $property) {
                return $key;
            }
        }

        return (new Convert($property))->toSnake();
    }
}
