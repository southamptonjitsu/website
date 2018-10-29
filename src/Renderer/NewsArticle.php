<?php

namespace SotonJitsu\Renderer;

use SotonJitsu\Markdown\Provider;
use SotonJitsu\News\Article;
use SotonJitsu\Template;

class NewsArticle
{
    private $mdProvider;

    private $template;

    public function __construct(Template $template, Provider $mdProvider)
    {
        $this->template = $template;
        $this->mdProvider = $mdProvider;
    }

    public function render(Article $article)
    {
        return $this->template->render([
            'title' => $article->getTitle(),
            'contents' => $this->mdProvider->fromText($article->getContents())
        ]);
    }
}