<?php

namespace Coyote\Services\Stream\Render;

class Group extends Render
{
    /**
     * @return string
     */
    public function name()
    {
        return $this->stream['object.displayName'];
    }

    /**
     * @return string
     */
    public function excerpt()
    {
        return '';
    }
}
