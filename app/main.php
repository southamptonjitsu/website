<?php

declare(strict_types=1);

use Http\Factory\Diactoros\ResponseFactory;
use Http\Factory\Diactoros\StreamFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use SotonJitsu\Http;
use SotonJitsu\Template;

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

function response(int $code, Template $content): Response
{
    return (new ResponseFactory())
        ->createResponse($code)
        ->withBody((new StreamFactory())->createStream($content->render()));
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

$app->add(deferMiddleware(new Http\Render(
        new StreamFactory(),
        new class implements Http\BuildTemplate
        {
            public function build(string $content): \SotonJitsu\Template
            {
                return template('page')
                    ->with('content', $content)
                    ->with('footer', renderFooter());
            }
        }
    )
));

$app->get('/', function () use ($mdReader, $newsProvider) {
    $articles = $newsProvider->readLast(3);

    $article = function (\SotonJitsu\News\Article $article, $contentLength = 300) use ($mdReader) {
        return template('home/news-article')
            ->with('title', $article->getTitle())
            ->with('content', substr(strip_tags($mdReader->fromText($article->getContents())), 0, $contentLength) . '...')
            ->with('url', "/news/{$article->getKey()}")
            ->with('imageUrl', $article->getImage());
    };

    return response(200, template('home')
        ->with('copy', $mdReader->fromFile(__DIR__ . '/../resources/pages/home/home.md'))
        ->with('article1', $article($articles[array_keys($articles)[0]]))
        ->with('article2', $article($articles[array_keys($articles)[1]])));
});

$app->get('/club', function () use ($mdReader) {
    return response(200, template('standard')
        ->with('title', 'The Club')
        ->with('copy', template('club')
            ->with('art', $mdReader->fromFile(__DIR__ . '/../resources/pages/club/art.md'))
            ->with('club', $mdReader->fromFile(__DIR__ . '/../resources/pages/club/club.md'))
        ));
});

$app->get('/news', function () use ($mdReader, $newsProvider) {
    $articles = $newsProvider->readLast(10);

    return response(200, template('standard')
        ->with('title', 'News')
        ->with('copy', array_reduce(
            array_keys($articles),
            function (string $acc, string $key) use ($articles, $mdReader) {
                $article = $articles[$key];

                return $acc . template('news/article')
                    ->with('title', $article->getTitle())
                    ->with('link', "/news/$key")
                    ->with('contents', $mdReader->fromText($article->getContents()))
                    ->with('data', $article->getDateTime()->format('M d, Y'));
            },
            ''
        ))
    );
});

$app->get('/news/{key:[a-z0-9\-]+}', function (Request $request) use (
    $mdReader,
    $newsProvider
) {
    $article = $newsProvider->read($request->getAttribute('key'));

    return response(200, template('standard')
        ->with('title', 'News')
        ->with('copy', template('news/article')
            ->with('title', $article->getTitle())
            ->with('contents', $mdReader->fromText($article->getContents()))
            ->with('date', $article->getDateTime()->format('M d, Y')))
    );
});

$app->run();
