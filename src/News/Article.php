<?php

namespace SotonJitsu\News;

class Article
{
    private $title;

    private $contents;

    private $dateTime;

    public function __construct($title, $contents, \DateTimeImmutable $dateTime)
    {
        $this->title = $title;
        $this->contents = $contents;
        $this->dateTime = $dateTime;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getImage()
    {

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
