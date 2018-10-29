<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SotonJitsu\Template;

$mdReader = new \SotonJitsu\Markdown\Provider(new Parsedown());

$newsProvider = new \SotonJitsu\News\Provider(
    __DIR__ . '/../resources/pages/news',
    new \SotonJitsu\Parser\Yaml\Yaml()
);

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

function renderFooter() {
    return (new Template(__DIR__ . '/../resources/templates/common/footer.phtml'))->render();
}

function renderPage($content) {
    return (new Template(__DIR__ . '/../resources/templates/page.phtml'))->render([
        'content' => $content,
        'footer'  => renderFooter(),
    ]);
};

$app->get('/', function (Request $request, Response $response) use (
    $mdReader,
    $newsProvider
) {
    $articles = $newsProvider->readLast(3);

    $article = function (\SotonJitsu\News\Article $article, $contentLength, $main = false) use ($mdReader) {
        return (new Template(__DIR__ . '/../resources/templates/home/news-article.phtml'))
            ->render([
               'main' => $main,
               'title' => $article->getTitle(),
               'content' => substr(strip_tags($mdReader->fromText($article->getContents())), 0, $contentLength) . '...',
            ]);
    };

    return $response->getBody()->write(
        renderPage((new Template(__DIR__ . '/../resources/templates/home.phtml'))
            ->render([
                'copy' => $mdReader->fromFile(__DIR__ . '/../resources/pages/home/home.md'),
                'article1' => $article($articles[array_keys($articles)[0]], 300, true),
                'article2' => $article($articles[array_keys($articles)[1]], 100),
                'article3' => $article($articles[array_keys($articles)[2]], 100),
            ])
        )
    );
});

$app->get('/news/{key:[a-z0-9\-]+}', function (Request $request, Response $response, array $args) use (
    $mdReader,
    $newsProvider
) {
    $article = $newsProvider->read($args['key']);

    return $response->getBody()->write(
        renderPage((new Template(__DIR__ . '/../resources/templates/standard.phtml'))
            ->render([
                'title' => 'News',
                'copy' => (new Template(__DIR__ . '/../resources/templates/news/article.phtml'))
                    ->render([
                        'title' => $article->getTitle(),
                        'contents' => $mdReader->fromText($article->getContents()),
                        'date'     => $article->getDateTime()->format('M d, Y'),
                    ]),
            ])
        )
    );
});

$app->run();
