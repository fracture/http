<?php

namespace Fracture\Http\Headers;

class Accept extends Common
{

    protected $fieldName = 'Accept';


    /**
     * @param string $headerValue
     * @return array[]
     */
    protected function extractData($headerValue)
    {
        $elements = preg_split('#,\s?#', $headerValue, -1, \PREG_SPLIT_NO_EMPTY);
        $elements = $this->obtainGroupedElements($elements);
        $keys = $this->obtainSortedQualityList($elements);
        return $this->obtainSortedElements($elements, $keys);
    }


    /**
     * @return array[]
     */
    public function getPrioritizedList()
    {
        return  $this->list;
    }


    /**
     * @param array $elements
     */
    private function obtainGroupedElements($elements)
    {
        $result = [];

        foreach ($elements as $item) {
            $item = $this->obtainAssessedItem($item);
            $quality = $item[ 'q' ];

            if (array_key_exists($quality, $result) === false) {
                $result[$quality] = [];
            }

            $result[$quality][] = $item;
        }

        return $result;
    }


    /**
     * @param string $item
     * @return array
     */
    private function obtainAssessedItem($item)
    {
        $result = [];
        $parts = preg_split('#;\s?#', $item, -1, \PREG_SPLIT_NO_EMPTY);
        $result['value'] = array_shift($parts);

        foreach ($parts as $item) {
            list($key, $value) = explode('=', $item . '=');
            $result[$key] = $value;
        }

        $result = $result + ['q' => '1'];

        return $result;
    }


    /**
     * @param array[] $elements
     * @return array[]
     */
    private function obtainSortedQualityList($elements)
    {
        $keys = array_keys($elements);
        $keys = array_map(function ($value) {
            return (float)$value;
        }, $keys);
        rsort($keys);
        return array_map(function ($value) {
            return (string)$value;
        }, $keys);
    }


    /**
     * @param array[] $elements
     * @param array $keys
     * @return array[]
     */
    private function obtainSortedElements($elements, $keys)
    {
        $list = [];

        foreach ($keys as $key) {
            foreach ($elements[$key] as $item) {
                unset($item['q']);
                $list[] = $item;
            }
        }

        return $list;
    }


    /**
     * @param string $type
     * @return bool
     */
    public function contains($type)
    {
        $expected = $this->obtainAssessedItem($type);
        unset($expected['q']);

        foreach ($this->list as $item) {
            if ($this->isMatch($expected, $item)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param string $options
     * @return null|string
     */
    public function getPreferred($options)
    {
        $options = $this->extractData($options);

        foreach ($this->list as $item) {
            $entry = $this->obtainEntryFromList($item, $options);

            if ($entry !== null) {
                return $this->getFormatedEntry($entry);
            }
        }

        return null;
    }


    /**
     * @param array $entry
     * @return string
     */
    public function getFormatedEntry($entry)
    {
        if (count($entry) === 1) {
            return $entry['value'];
        }

        $value = $entry['value'];
        unset($entry['value']);

        array_walk($entry, function (&$item, $key) {
            $item = $key . '=' . $item;
        });
        return $value . ';' .  implode(';', $entry);
    }


    /**
     * @param array $needle
     * @param array[] $haystack
     * @return null|array
     */
    private function obtainEntryFromList(array $needle, $haystack)
    {
        foreach ($haystack as $item) {
            if ($this->isMatch($item, $needle)) {
                return $item;
            }
        }

        return null;
    }


    /**
     * @param string $left
     * @param string $right
     * @return bool
     */
    private function isMatch(array $left, array $right)
    {
        if ($left == $right) {
            return true;
        }

        $left['value'] = $this->replaceStars($left['value'], $right['value']);
        $right['value'] = $this->replaceStars($right['value'], $left['value']);

        // compares two arrays with keys in different order
        return $left == $right;
    }


    /**
     * @param string $target
     * @param string pattern
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
