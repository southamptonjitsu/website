<?php

declare(strict_types=1);

namespace SotonJitsu\Http;

use SotonJitsu\Template;

interface BuildTemplate
{
    public function build(string $content): Template;
}
