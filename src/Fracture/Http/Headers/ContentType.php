<?php

namespace Fracture\Http\Headers;

class ContentType extends Common
{

    protected $headerName = 'Content-Type';


    /**
     * @param string $headerValue
     * @return array
     */
    protected function extractData($headerValue)
    {
        $result = [];
        $parts = preg_split('#;\s?#', $headerValue, -1, \PREG_SPLIT_NO_EMPTY);

        if (count($parts) === 0) {
            return [];
        }

        $result['value'] = array_shift($parts);

        foreach ($parts as $item) {
            list($key, $value) = explode('=', $item . '=');
            $result[ $key ] = $value;
        }

        return $result;
    }


    /**
     * @param string $type
     * @return bool
     */
    public function contains($type)
    {
        return array_key_exists('value', $this->data) && $this->data['value'] === $type;
    }


    public function match($type)
    {
        if ($this->contains($type)) {
            return true;
        }

        return $this->isCompatible($this->data['value'], $type);
    }


    private function isCompatible($target, $pattern)
    {
        return $this->replaceStars($target, $pattern) === $this->replaceStars($pattern, $target);
    }


    /**
     * @param string $target
     * @param string $pattern
     * @return string
     */
    private function replaceStars($target, $pattern)
    {
        $target = explode('/', $target . '/*');
        $pattern = explode('/', $pattern . '/*');

        if ($pattern[0] === '*') {
            $target[0] = '*';
        }

        if ($pattern[1] === '*') {
            $target[1] = '*';
        }

        return $target[0] . '/' . $target[1];
    }
}
