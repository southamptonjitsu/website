<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SotonJitsu\Template;

function resourcePath($file) {
    return __DIR__ . '/../resources/' . $file;
}

function template($path) {
    return new Template(resourcePath("templates/$path.phtml"));
}

function renderFooter() {
    $config = require __DIR__ . '/../oauth.php';

    $twitter = new SotonJitsu\Twitter\Reader(
        new \GuzzleHttp\Client(['http_errors' => false]),
        new \SotonJitsu\Twitter\Credentials($config->twitter->id, $config->twitter->secret),
        new \SotonJitsu\Twitter\Cache\File(__DIR__ . '/../cache.txt')
    );

    return template('common/footer')->render([
        'tjfTweet' => $twitter->getLatestTweet('132799450'),
    ]);
}

function renderPage($content) {
    return template('page')->render([
        'content' => $content,
        'footer'  => renderFooter(),
    ]);
};

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

$app->get('/', function (Request $request, Response $response) use (
    $mdReader,
    $newsProvider
) {
    $articles = $newsProvider->readLast(3);

    $article = function (\SotonJitsu\News\Article $article, $contentLength = 300) use ($mdReader) {
        return template('home/news-article')->render([
            'title' => $article->getTitle(),
            'content' => substr(strip_tags($mdReader->fromText($article->getContents())), 0, $contentLength) . '...',
            'url'     => "/news/{$article->getKey()}",
            'imageUrl' => $article->getImage(),
        ]);
    };

    return $response->getBody()->write(
        renderPage(template('home')->render([
            'copy' => $mdReader->fromFile(__DIR__ . '/../resources/pages/home/home.md'),
            'article1' => $article($articles[array_keys($articles)[0]]),
            'article2' => $article($articles[array_keys($articles)[1]]),
        ]))
    );
});

$app->get('/news', function (Request $request, Response $response) use ($mdReader, $newsProvider) {
    $articles = $newsProvider->readLast(10);

    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'News',
            'copy'  => array_reduce(
                array_keys($articles),
                function ($acc, $key) use ($articles, $mdReader) {
                    $article = $articles[$key];

                    return $acc . template('news/article')->render([
                            'title'     => $article->getTitle(),
                            'link'      => "/news/$key",
                            'contents'  => $mdReader->fromText($article->getContents()),
                            'date'      => $article->getDateTime()->format('M d, Y'),
                        ]) . '<hr/>';
                },
                ''
            ),
        ]))
    );
});

$app->get('/news/{key:[a-z0-9\-]+}', function (Request $request, Response $response, array $args) use (
    $mdReader,
    $newsProvider
) {
    $article = $newsProvider->read($args['key']);

    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'News',
            'copy' => template('news/article')->render([
                'title' => $article->getTitle(),
                'contents' => $mdReader->fromText($article->getContents()),
                'date'     => $article->getDateTime()->format('M d, Y'),
            ]),
        ]))
    );
});

$app->get('/art', function (Request $request, Response $response) use ($mdReader) {
    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'The Art',
            'copy'  => template('art')->render([
                'introduction' => $mdReader->fromFile(__DIR__ . '/../resources/pages/art/introduction.md'),
                'foundation'   => $mdReader->fromFile(__DIR__ . '/../resources/pages/art/foundation.md'),
            ]),
        ]))
    );
});

$app->get('/club', function (Request $request, Response $response) use ($mdReader) {
    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'The Club',
            'copy'  => template('club')->render([
                'introduction' => $mdReader->fromFile(__DIR__ . '/../resources/pages/club/introduction.md'),
                'members'      => $mdReader->fromFile(__DIR__ . '/../resources/pages/club/members.md'),
            ]),
        ]))
    );
});

$app->get('/pricing', function (Request $request, Response $response) use ($mdReader) {
    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'Pricing',
            'copy'  => $mdReader->fromFile(__DIR__ . '/../resources/pages/pricing/pricing.md'),
        ]))
    );
});

$app->get('/contact', function (Request $request, Response $response) use ($mdReader) {
    return $response->getBody()->write(
        renderPage(template('standard')->render([
            'title' => 'Contact',
            'copy'  => $mdReader->fromFile(__DIR__ . '/../resources/pages/contact/contact.md'),
        ]))
    );
});

$app->run();
