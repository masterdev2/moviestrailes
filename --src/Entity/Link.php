<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="links")
 */
class Link
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
    private $link;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $lang;

    /**
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Show",cascade={"persist"} )
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=true)
     */
    private $show;

    /**
     * @var Episode
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Episode")
     * @ORM\JoinColumn(name="episode_id", referencedColumnName="id", nullable=true)
     */
    private $episode;

    public function __construct()
    {
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
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Show
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param Show $show
     *
     * @return self
     */
    public function setShow(Show $show)
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @return Episode
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * @param Episode $episode
     *
     * @return self
     */
    public function setEpisode(Episode $episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     *
     * @return self
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }
}