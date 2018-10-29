<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$mdReader = new \SotonJitsu\Markdown\Provider(new Parsedown());

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

function renderFooter() {
    return (new \SotonJitsu\Template(__DIR__ . '/../resources/templates/common/footer.phtml'))->render();
}

function renderPage($content) {
    return (new \SotonJitsu\Template(__DIR__ . '/../resources/templates/master.phtml'))->render([
        'content' => $content,
        'footer'  => renderFooter(),
    ]);
};

$app->get('/', function (Request $request, Response $response) use ($mdReader) {
    return $response->getBody()->write(
        renderPage((new \SotonJitsu\Template(__DIR__ . '/../resources/templates/home.phtml'))
            ->render([
                'copy' => $mdReader->fromFile(__DIR__ . '/../resources/pages/home/home.md'),
            ])
        )
    );
});

$app->get('/news/{key:[a-z0-9\-]+}', function (Request $request, Response $response, array $args) use ($mdReader) {
    $provider = new \SotonJitsu\News\Provider(
        __DIR__ . '/../resources/pages/news',
        new \SotonJitsu\Parser\Yaml\Yaml()
    );

    $article = $provider->readArticle($args['key']);

    $articleRenderer = new \SotonJitsu\Renderer\NewsArticle(
        new \SotonJitsu\Template(__DIR__ . '/../resources/templates/news/article.phtml'),
        $mdReader
    );

    return $response->getBody()->write(
        renderPage((new \SotonJitsu\Template(__DIR__ . '/../resources/templates/home.phtml'))
            ->render([
                'copy' => $articleRenderer->render($article),
            ])
        )
    );
});

$app->run();
