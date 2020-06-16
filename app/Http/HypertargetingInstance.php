<?php

namespace brain\Http;

class HypertargetingInstance extends \stdClass
{
    public function __construct($initialConfig)
    {
        foreach ($initialConfig as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
} 