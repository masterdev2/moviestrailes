<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="episodes")
 */
class Episode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $imdb;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Blank()
     */
    private $tmdb;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Blank()
     */
    private $year;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Season")
     * @ORM\JoinColumn(name="season_id", referencedColumnName="id", nullable=true)
     */
    private $season;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Link", mappedBy="show", cascade={"persist", "remove"})
     */
    private $links;


    public function __construct()
    {
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImdb()
    {
        return $this->imdb;
    }

    /**
     * @param mixed $imdb
     *
     * @return self
     */
    public function setImdb($imdb)
    {
        $this->imdb = $imdb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTmdb()
    {
        return $this->tmdb;
    }

    /**
     * @param mixed $tmdb
     *
     * @return self
     */
    public function setTmdb($tmdb)
    {
        $this->tmdb = $tmdb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     *
     * @return self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return User
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param User $season
     *
     * @return self
     */
    public function setSeason(Season $season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param mixed $links
     *
     * @return self
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }
}