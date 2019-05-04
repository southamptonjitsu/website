<?php

declare(strict_types=1);

namespace SotonJitsu\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SotonJitsu\Template;

final class Renderer implements MiddlewareInterface
{
    /** @var Template */
    private $base;

    /** @var callable */
    private $getFooter;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory, Template $template, callable $getFooter)
    {
        $this->streamFactory = $streamFactory;
        $this->base = $template;
        $this->getFooter = $getFooter;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $response->withBody($this->streamFactory->createStream($this->base->render([
            'content' => $response->getBody(),
            'footer'  => ($this->getFooter)(),
        ])));
    }
}
