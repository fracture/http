<?php

namespace Fracture\Http\Parsers;

class DataForm
{

    private $input;
    private $boundry;

    private $factory;

    private $data = [];
    private $stage = null;


    public function __construct(\Fracture\Http\HeaderFactory $factory, $input, $boundry = null)
    {
        $this->factory = $factory;
        $this->input = $input;
        $this->boundry = $boundry;
    }


    public function prepare()
    {
        try {
            $file = new \SPLFileObject($this->input);

            $this->boundry = $this->guessBoundry($file, $this->boundry);

            foreach ($this->collectParameters($file, $this->boundry) as $key => $value) {
                $this->data[$key] = $value;
            }

        } catch (\RuntimeException $e) {
            return false;
        }

    }


    private function guessBoundry($file, $boundry)
    {
        if (false !== strpos($file->current(), $boundry)) {
            return trim($file->current(), "\r\n");
        }
        return null;
    }

    private function collectParameters(\SPLFileObject $file, $boundry)
    {
        $name = null;
        $value = null;

        foreach ($file as $line) {
            if ($line === $boundry .  "--\r\n") {
                // .. then this is the last line
                break;
            }

            if ($line === $boundry . "\r\n") {
                $this->stage = 'headers';
                continue;
            }

            if ($line === "\r\n") {
                $this->stage = 'body';
                continue;
            }

            switch ($this->stage) {
                case 'headers':
                    $entry = $this->readHeader($line);
                    if ($entry && $entry->getFieldName() === 'Content-Disposition') {
                        $name = $entry->getAttribute('name');
                    }
                    break;

                case 'body':
                    yield $name => trim($line, "\r\n");
                    break;
            }
        }
    }


    private function readHeader($line)
    {
        $line = trim($line, "\r\n");
        return $this->factory->create($line);
    }


    private function readBody($line)
    {

    }

    public function getParameter($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }
}
