<?php

namespace SotonJitsu\News;

use SotonJitsu\Exception\CannotLoadFile;
use SotonJitsu\Exception\InvalidMetadata;
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

    /**
     * @param int $count
     * @return Article[]
     */
    public function readLast($count)
    {
        if (!is_int($count)) {
            throw new InvalidType('Passed count must be an integer');
        }

        return array_reduce(
            array_slice(
                array_diff(scandir($this->directory, 1), ['.', '..']),
                0,
                $count
            ),
            function ($acc, $key) {
                $acc[$key] = $this->read($key);
                return $acc;
            },
            []
        );
    }

    public function read($key)
    {
        if (!is_string($key)) {
            throw new InvalidType('Passed key must be a string');
        }

        return $this->makeArticle("{$this->directory}/$key");
    }

    private function makeArticle($directory)
    {
        $contentFile = "$directory/article.md";

        if (!file_exists($contentFile) || !is_readable($contentFile)) {
            throw new CannotLoadFile("Unable to load article contents from $contentFile");
        }

        $metadata = $this->yamlParser->fromFile("$directory/meta.yaml");

        $this->validateMetadata($metadata);

        return new Article(
            $metadata['title'],
            file_get_contents($contentFile),
            (new \DateTimeImmutable())->setTimestamp(strtotime($metadata['datetime']))
        );
    }

    private function validateMetadata(array $metadata)
    {
        foreach (['title', 'datetime', 'description'] as $key) {
            if (!isset($metadata[$key]) || !is_string($metadata[$key])) {
                throw new InvalidMetadata("Key $key did not exist or was not a string in the supplied metadata");
            }
        }
    }
}
