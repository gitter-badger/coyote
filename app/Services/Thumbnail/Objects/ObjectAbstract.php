<?php

namespace Coyote\Services\Thumbnail\Objects;

use Imagine\Image;

/**
 * Class Object
 * @package Coyote\Services\Thumbnail\Objects
 */
abstract class ObjectAbstract implements ObjectInterface
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @return Image\Box
     */
    public function getBox()
    {
        return new Image\Box($this->width, $this->height);
    }

    /**
     * @return string
     */
    public function getInterface()
    {
        return Image\ImageInterface::THUMBNAIL_OUTBOUND;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
