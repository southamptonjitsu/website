<?php

namespace SotonJitsu\Markdown;

use SotonJitsu\Exception\CannotLoadFile;

class Provider
{
    private $parsedown;

    public function __construct(\Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    /**
     * @param string $filepath
     *
     * @return string
     */
    public function fromFile($filepath)
    {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw new CannotLoadFile("Unable to read file: $filepath");
        }

        return $this->parsedown->text(file_get_contents($filepath));
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function fromText($text)
    {
        return $this->parsedown->text($text);
    }
}