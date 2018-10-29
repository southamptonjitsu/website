<?php

namespace SotonJitsu\News;

class Article
{
    private $title;

    private $contents;

    public function __construct($title, $contents)
    {
        $this->title = $title;
        $this->contents = $contents;
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
}
