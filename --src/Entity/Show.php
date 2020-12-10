<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="shows")
 */
class Show
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
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
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Blank()
     */
    private $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Blank()
     */
    private $genre;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $poster;

    /**
     * @ORM\Column(type="integer", length=100, nullable=true)
     */
    private $seasons;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Season", mappedBy="show", cascade={"persist", "remove"})
     */
    private $seasns;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Link", mappedBy="show", cascade={"persist", "remove"})
     */
    private $links;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Title", mappedBy="show", cascade={"persist", "remove"})
     */
    private $titles;


    public function __construct()
    {
    }

    public function addTitle(Title $title)
    {
        $this->titles[] = $title;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
    }

    public function addSeason(Season $season)
    {
        $this->seasns[] = $season;
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
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     *
     * @return self
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param mixed $poster
     *
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * @param mixed $seasons
     *
     * @return self
     */
    public function setSeasons($seasons)
    {
        $this->seasons = $seasons;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeasns()
    {
        return $this->seasns;
    }

    /**
     * @param mixed $seasns
     *
     * @return self
     */
    public function setSeasns($seasns)
    {
        $this->seasns = $seasns;

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

    /**
     * @return mixed
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param mixed $titles
     *
     * @return self
     */
    public function setTitles($titles)
    {
        $this->titles = $titles;

        return $this;
    }
}