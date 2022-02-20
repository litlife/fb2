<?php

namespace Litlife\Fb2;

class FictionBook extends Tag
{
    public function description(): Tag
    {
        return $this->getFb2()->description();
    }
}
