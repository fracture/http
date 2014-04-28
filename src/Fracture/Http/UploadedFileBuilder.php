<?php

namespace Fracture\Http;

class UploadedFileBuilder
{

    /**
     * @param array $params
     * @return UploadedFile
     */
    public function create($params)
    {
        $instance = $this->buildInstance($params);
        $instance->prepare();

        return $instance;
    }


    protected function buildInstance($params)
    {
        return new UploadedFile($params);
    }
}
