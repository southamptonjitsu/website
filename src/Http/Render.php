<?php

declare(strict_types=1);

namespace SotonJitsu\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SotonJitsu\Template;

final class Render implements MiddlewareInterface
{
    /** @var BuildTemplate */
    private $buildTemplate;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory, BuildTemplate $buildTemplate)
    {
        $this->buildTemplate = $buildTemplate;
        $this->streamFactory = $streamFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $response->withBody(
            $this->streamFactory->createStream(
                $this->buildTemplate->build((string)$response->getBody())->render()
            )
        );
    }
}
