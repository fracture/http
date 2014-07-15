<?php

namespace Fracture\Http;

class HeaderFactory
{

    public function splitEntry($header)
    {
        $separator = strpos($header, ': ');

        if ($separator) {
            return [
                substr($header, 0, $separator),
                substr($header, $separator + 2),
            ];
        }

        return null;
    }

}
