<?php

namespace Coyote\Services\Media;

use Coyote\Services\Media\Factories\AbstractFactory as MediaFactory;

interface MediaInterface
{
    /**
     * @return MediaFactory
     */
    public function getFactory();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param string $filename
     */
    public function setFilename($filename);

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDownloadUrl();

    /**
     * @param string $downloadUrl
     * @return $this
     */
    public function setDownloadUrl($downloadUrl);

    /**
     * @return string
     */
    public function path();

    /**
     * @return string
     */
    public function url();

    /**
     * @param mixed $content
     */
    public function put($content);

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return int
     */
    public function size();

    /**
     * @return bool
     */
    public function isImage();

    /**
     * @return bool
     */
    public function delete();
}
