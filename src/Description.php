<?php

namespace Litlife\Fb2;

class Description extends Tag
{
    public function title_info(): Tag|null
    {
        return $this->getFirstChild('title-info');
    }
}
