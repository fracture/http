<?php

namespace Fracture\Http\Headers;

class Accept extends Common
{

    protected $headerName = 'Accept';


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
            $sorted = $this->sortBySpecificity($elements[$key]);
            foreach ($sorted as $item) {
                unset($item['q'], $item[' spec ']);
                $list[] = $item;
            }
        }

        return $list;
    }


    private function sortBySpecificity($list)
    {

        foreach ($list as $key => $item) {
            $list[$key][' spec '] = $this->computeSpecificity($item);
        }

        uksort($list, function($a, $b) use ($list) {
            if ($list[$a][' spec '] === $list[$b][' spec ']) {
                return $a > $b ? 1 : -1;
            }

            return $list[$a][' spec '] > $list[$b][' spec '] ? -1 : 1;
        });

        return $list;
    }


    private function computeSpecificity($entry)
    {
        list($type, $subtype) = explode('/', $entry['value'] . '/');
        $specificity = count($entry) - 2;

        if ($type !== '*') {
            $specificity += 1000;
        }

        if ($subtype !== '*') {
            $specificity += 100;
        }

        return $specificity;
    }


    /**
     * @param string $type
     * @return bool
     */
    public function contains($type)
    {
        $expected = $this->obtainAssessedItem($type);
        unset($expected['q']);

        if ($this->data === null) {
            return false;
        }

        return $this->matchFound($this->data, $expected);
    }


    /**
     * @param array $data
     * @param array $expected
     * @return bool
     */
    private function matchFound($data, $expected)
    {
        foreach ($data as $item) {
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
        $data = $this->extractData($options);

        if ($this->data === null) {
            return null;
        }

        return $this->findFormatedEntry($this->data, $data);
    }


    /**
     * @param array $data
     * @param array $options
     * @return null|string
     */
    private function findFormatedEntry($data, $options)
    {
        foreach ($data as $item) {
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
