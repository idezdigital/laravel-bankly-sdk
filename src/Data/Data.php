<?php

namespace Idez\Bankly\Data;

use Idez\Bankly\Traits\Makeable;
use Psr\Http\Message\ResponseInterface;

abstract class Data implements \JsonSerializable
{
    use Makeable;

    /**
     * Private internal struct attributes
     */
    protected array|ResponseInterface $attributes = [];

    public function __construct(array|ResponseInterface $data = [])
    {
        if ($data instanceof ResponseInterface) {
            $data = $this->toJson($data, true);
        }

        foreach ($data as $key => $val) {
            if (property_exists(static::class, $key)) {
                $this->$key = $val;

                continue;
            }

            $this->attributes[$key] = $val;
        }
    }

    /**
     * @param ResponseInterface $response
     * @param bool $assoc
     * @return mixed
     */
    public function toJson(ResponseInterface $response, bool $assoc = false): mixed
    {
        $contents = $response->getBody()->getContents();

        return json_decode($contents, $assoc);
    }

    /**
     * Set a value
     * @param string $key
     * @param mixed $value
     */
    public function __set(string $key, mixed $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get a value
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Check if a key is set
     * @param string $key
     * @return bool
     */
    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $vars = get_object_vars($this);
        $array = [];

        foreach ($vars as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    public function jsonSerialize(): ResponseInterface|array
    {
        return $this->attributes;
    }
}
