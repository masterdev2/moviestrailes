<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="seasons")
 */
class Season
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
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Blank()
     */
    private $year;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Show")
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=true)
     */
    private $show;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Episode", mappedBy="season", cascade={"persist", "remove"})
     */
    private $episodes;

    public function __construct()
    {
    }

    public function addEpisodes(Episode $episode)
    {
        $this->episodes[] = $episode;
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
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param User $show
     *
     * @return self
     */
    public function setShow(Show $show)
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * @param mixed $episodes
     *
     * @return self
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;

        return $this;
    }
}