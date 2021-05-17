<?php

namespace Pishran\IpPanel\Models;

abstract class Base
{
    /**
     * @param  mixed  $data
     */
    public function fromJson($data): Base
    {
        foreach ($data as $key => $value) {
            $camelCased = $this->snakeToCamel($key);

            if (property_exists($this, $camelCased)) {
                $this->$camelCased = $value;
            }
        }

        return $this;
    }

    protected function snakeToCamel(string $text): string
    {
        return lcfirst(str_replace('_', '', ucwords($text, '_')));
    }
}
