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
     * @var Show
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Show")
     * @ORM\JoinColumn(name="show_id", referencedColumnName="id", nullable=false)
     */
    private $show;

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
}