<?php

namespace SotonJitsu\News;

use SotonJitsu\Exception\CannotLoadFile;
use SotonJitsu\Exception\InvalidType;
use SotonJitsu\Parser\Yaml\Yaml as YamlParser;

class Provider
{
    private $directory;

    private $yamlParser;

    public function __construct($directory, YamlParser $yamlParser)
    {
        if (!is_string($directory)) {
            throw new InvalidType('Directory name must be a string');
        }

        if (!file_exists($directory) || !is_readable($directory)) {
            throw new CannotLoadFile("Directory path $directory not readable");
        }

        $this->directory = $directory;
        $this->yamlParser = $yamlParser;
    }

    public function readArticle($key)
    {
        $directory = "{$this->directory}/$key";

        if (!file_exists($directory) || !is_readable($directory)) {
            return null;
        }

        $metadata = "$directory/meta.yaml";
        $contents = "$directory/article.md";

        if (!file_exists($metadata) || !is_readable($metadata)) {
            throw new CannotLoadFile('Unable to load metadata file');
        }

        if (!file_exists($contents) || !is_readable($contents)) {
            throw new CannotLoadFile('Unable to load contents file');
        }

        $metadata = $this->yamlParser->fromFile($metadata);
        $contents = file_get_contents($contents);

        return new Article($metadata['title'], $contents);
    }
}
