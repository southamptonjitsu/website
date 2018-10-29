<?php

namespace SotonJitsu\Parser\Yaml;

use SotonJitsu\Exception\CannotLoadFile;

class Yaml
{
    /**
     * @param string $filepath
     *
     * @return array
     */
    public function fromFile($filepath)
    {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw new CannotLoadFile("Unable to read file: $filepath");
        }

        return \Symfony\Component\Yaml\Yaml::parse(file_get_contents($filepath));
    }
}
