<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *     "id": "exact",
 *     "title": "partial",
 *     "content": "partial",
 *     "author": "exact",
 *     "priority": "exact"
 *     }
 * )
 * @ApiFilter(
 *     DateFilter::class,
 *     properties={
 *     "date"
 *     }
 * )
 * @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *     "id"
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *     "id",
 *     "title",
 *     "date",
 *     "priority"
 *     },
 *     arguments={"orderParameterName"="_order"}
 * )
 * @ApiFilter(
 *     PropertyFilter::class,
 *     arguments={
 *     "parameterName": "properties",
 *     "overrideDefaultProperties": false,
 *     "whitelist":{"id","data","slug","priority","content","title","author"}
 *     }
 * )
 * @ApiResource(
 *     attributes={"order"={"priority":"DESC"}, "maximum_items_per_page"=24},
 *     itemOperations={
 *                      "delete"={
 *                          "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user"
 *                      },
 *                      "get"={
 *                          "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user",
 *                          "normalization_context"={"groups"={"get-project-with-author"}}
 *                      },
 *                      "put"={
 *                          "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user"
 * }
 *                  },
 *     collectionOperations={
 *     "get"={"access_control"="is_granted('ROLE_USER')"},
 *     "post" ={"access_control"="is_granted('ROLE_USER')"}
 *     },
 *     denormalizationContext={
 *     "groups"={"post"}
 *     }
 * )
 */
class Project implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-project-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @Groups({"post", "get-project-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-project-with-author"})
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"post","get-project-with-author"})
     * @Assert\NotBlank()
     */

    private $dateEnd;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @Groups({"post","get-project-with-author"})
     */
    private $content;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-project-with-author"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post","get-project-with-author"})
     */
    private $slug;


    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="5")
     * @Groups({"post","get-project-with-author"})
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project")
     * @ApiSubresource()
     * @Groups({"get-project-with-author"})
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image")
     * @ORM\JoinTable()
     * @ApiSubresource()
     * @Groups({"post","get-project-with-author"})
     */
    private $images;

    public function __construct()
    {
        $this->tasks= new ArrayCollection();
        $this->images= new ArrayCollection();
    }

    public function getTasks() : Collection
    {
        return $this->tasks;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): PublishedDateEntityInterface
    {
        $this->date = $date;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor() : ?User
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;
        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image)
    {
        $this->images->add($image);
    }

    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    public function setDateEnd($dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    public function __toString() : string
    {
        return $this->title;
    }
}
