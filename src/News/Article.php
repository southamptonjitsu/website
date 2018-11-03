<?php

namespace SotonJitsu\News;

class Article
{
    private $key;

    private $title;

    private $contents;

    private $dateTime;

    private $image = null;

    public function __construct($key, $title, $contents, \DateTimeImmutable $dateTime, $image = null)
    {
        $this->key = $key;
        $this->title = $title;
        $this->contents = $contents;
        $this->dateTime = $dateTime;
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return isset($this->image);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }
}
