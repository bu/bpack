<?php

namespace bPack;

class Foundation
{
    protected bool $isDevMode = false;

    public function __construct($options)
    {

    }

    public function isDevMode(): bool
    {
        return false;
    }
}
