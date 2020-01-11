<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use SotonJitsu\Http;

function deferMiddleware(Middleware $middleware): callable
{
    return function (Request $request, Response $response, callable $next) use ($middleware) {
        return $middleware->process($request, new \Tuupola\Middleware\CallableHandler($next, $response));
    };
}

function resourcePath($file)
{
    return __DIR__ . '/../resources/' . $file;
}

function template($path)
{
    return new \SotonJitsu\Template(resourcePath("templates/$path.phtml"));
}

function renderFooter()
{
    $config = require __DIR__ . '/../oauth.php';

    $twitter = new SotonJitsu\Twitter\Reader(
        new \GuzzleHttp\Client(['http_errors' => false]),
        new \SotonJitsu\Twitter\Credentials($config->twitter->id, $config->twitter->secret),
        new \SotonJitsu\Twitter\Cache\File(__DIR__ . '/../cache.txt')
    );

    if (!isset($config->twitter)) {
        $twitter->disable();
    }

    return template('common/footer')->render([
        'tjfTweet' => $twitter->getLatestTweet('132799450'),
    ]);
}

$mdReader = new \SotonJitsu\Markdown\Provider(new Parsedown());

$newsProvider = new \SotonJitsu\News\Provider(
    __DIR__ . '/../resources/news',
    new \SotonJitsu\Parser\Yaml\Yaml()
);

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('DEBUG'),
    ]
]);

$app->add(deferMiddleware(new Http\Renderer(
    new \Http\Factory\Diactoros\StreamFactory(),
    template('page'),
    function () {
        return renderFooter();
    }
)));

$app->get('/', function () use ($mdReader, $newsProvider) {
    $articles = $newsProvider->readLast(3);

    $article = function (\SotonJitsu\News\Article $article, $contentLength = 300) use ($mdReader) {
        return template('home/news-article')->render([
            'title' => $article->getTitle(),
            'content' => substr(strip_tags($mdReader->fromText($article->getContents())), 0, $contentLength) . '...',
            'url' => "/news/{$article->getKey()}",
            'imageUrl' => $article->getImage(),
        ]);
    };

    return template('home')->render([
        'copy' => $mdReader->fromFile(__DIR__ . '/../resources/pages/home/home.md'),
        'article1' => $article($articles[array_keys($articles)[0]]),
        'article2' => $article($articles[array_keys($articles)[1]]),
    ]);
});

$app->get('/news', function () use ($mdReader, $newsProvider) {
    $articles = $newsProvider->readLast(10);

    return template('standard')->render([
        'title' => 'News',
        'copy' => array_reduce(
            array_keys($articles),
            function ($acc, $key) use ($articles, $mdReader) {
                $article = $articles[$key];

                return $acc . template('news/article')->render([
                        'title' => $article->getTitle(),
                        'link' => "/news/$key",
                        'contents' => $mdReader->fromText($article->getContents()),
                        'date' => $article->getDateTime()->format('M d, Y'),
                    ]);
            },
            ''
        ),
    ]);
});

$app->get('/news/{key:[a-z0-9\-]+}', function (Request $request) use (
    $mdReader,
    $newsProvider
) {
    $article = $newsProvider->read($request->getAttribute('key'));

    return template('standard')->render([
        'title' => 'News',
        'copy' => template('news/article')->render([
            'title' => $article->getTitle(),
            'contents' => $mdReader->fromText($article->getContents()),
            'date' => $article->getDateTime()->format('M d, Y'),
        ]),
    ]);
});

$app->get('/club', function () use ($mdReader) {
    return template('standard')->render([
        'title' => 'The Club',
        'copy' => template('club')->render([
            'art' => $mdReader->fromFile(__DIR__ . '/../resources/pages/club/art.md'),
            'club' => $mdReader->fromFile(__DIR__ . '/../resources/pages/club/club.md'),
        ]),
    ]);
});

$app->run();
