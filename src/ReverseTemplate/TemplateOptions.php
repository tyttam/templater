<?php

namespace app\ReversTemplate;

class TemplateOptions
{
    private $decode;

    public function setDecode(bool $with_decode): void
    {
        $this->decode = $with_decode;
    }

    public function getDecode(): bool
    {
        return $this->decode;
    }
}
