<?php

namespace App\Builder;

use App\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use App\Builder\UserBuilder;
use App\Finder\UserFinder;
use App\Finder\ResidenceFinder;
use App\Finder\NewsTypeFinder;

class NewsBuilder
{
    protected $userBuilder;
    protected $userFinder;
    protected $residenceFinder;
    protected $newsTypeFinder;

    public function __construct(UserBuilder $userBuilder, UserFinder $userFinder, ResidenceFinder $residenceFinder, NewsTypeFinder $newsTypeFinder)
    {
        $this->userBuilder = $userBuilder;
        $this->userFinder = $userFinder;
        $this->residenceFinder = $residenceFinder;
        $this->newsTypeFinder = $newsTypeFinder;
    }

    /**
     * @param News[] $news
     * @return array
     */
    public function buildList(array $news)
    {
        $dataResponse = array();
        foreach ($news as $new) {
            $dataResponse[] = $this->build($new);
        }

        return $dataResponse;
    }

    /**
     * @param News $news
     * @return array
     */
    public function build($new)
    {
        return array(
            'id' => $new->getId(),
            'title' => $new->getTitle(),
            'content' => $new->getContent(),
            'creation_date' => $new->getCreationDate() ? $new->getCreationDate()->format('d-m-Y H:i:s') : null,
            'author' => $this->userBuilder->buildSimple($new->getAuthor()),
            'residence' => $new->getResidence()->getId(),
            'picture' => $new->getPicture()?: '',
            'type' => $new->getType()->getDescription()
        );
    }

    public function buildFreshNews(Request $request)
    {
        $news = new News();
        return $this->buildNews($news, $request);
    }

    /**
     * @param News $news
     * @return array
     */
    public function buildNews(news $news, Request $request)
    {
        $news->setTitle($request->get('title') ?: $news->getTitle());
        $news->setContent($request->get('content') ?: $news->getContent());

        if ($request->get('author_id')) {
            $author = $this->userFinder->findUser($request->get('author_id'));
            $news->setAuthor($author);
        }

        if ($request->get('residence_id')) {
            $residence = $this->residenceFinder->findResidence($request->get('residence_id'));
            $news->setResidence($residence);
        }

        if ($request->get('type')) {
            $type = $this->newsTypeFinder->findTypeByDesc($request->get('type'));
            $news->setType($type);
        }
        
        return $news;
    }
}
