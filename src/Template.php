<?php

namespace SotonJitsu;

use SotonJitsu\Exception\CannotLoadFile;

class Template
{
    private $filepath;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function render($data = [])
    {
        if (is_array($data)) {
            $data = (object)$data;
        }

        $sandbox = \Closure::bind(
            function () {
                ob_start();

                $included = include '' . func_get_arg(0);

                if ($included === false) {
                    $script = func_get_arg(0);

                    if (!is_file($script)) {
                        throw new CannotLoadFile("File {$script} not found at run time");
                    }

                    if (!is_readable($script)) {
                        throw new CannotLoadFile("File at {$script} is not readable at run time");
                    }
                }

                return ob_get_clean();
            },
            $data
        );


        try {
            return $sandbox($this->filepath);

        } catch (\Exception $exception) {
            ob_end_clean();

            throw $exception;
        }
    }
}
