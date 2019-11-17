<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *     "order"={"done":"ASC","taskPriority":"DESC"},
 *     "pagination_client_enabled"=true,
 *     "pagination_client_items_per_page"=true
 *     },
 *     itemOperations={
 *                      "delete"={"access_control"="is_granted('ROLE_USER') and object.getAuthor() == user"},
 *                      "get",
 *                      "put"={
 *                          "access_control"="is_granted('ROLE_USER') and object.getAuthor() == user"
 * }
 *                  },
 *     collectionOperations={
 *     "get",
 *     "post" ={"access_control"="is_granted('ROLE_USER')",
 *              "normalization_context"={
 *                          "groups"={"get-task-with-author"}
 *                  }
 *     }
 *     },
 *     subresourceOperations={
 *          "api_projects_tasks_get_subresource"={
 *              "method"="GET",
 *              "normalization_context"={
 *                 "groups"={"get-task-with-author"}
 *             }
 *          }
 *     },
 *     denormalizationContext={
 *     "groups"={"post"}
 *     }
 *)
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task implements AuthoredEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-task-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post","get-task-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=1000)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get-task-with-author", "post"})
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-task-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $project;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post","get-task-with-author"})
     */
    private $done;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="5")
     * @Groups({"post","get-task-with-author"})
     */
    private $taskPriority;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"post","get-task-with-author"})
     */
    private $dateEndTask;

    public function __construct()
    {
        $this->done = false;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setDone($done): void
    {
        $this->done = $done;
    }

    public function getDateEndTask()
    {
        return $this->dateEndTask;
    }

    public function setDateEndTask($dateEndTask): void
    {
        $this->dateEndTask = $dateEndTask;
    }


    public function getTaskPriority()
    {
        return $this->taskPriority;
    }

    public function setTaskPriority($taskPriority): void
    {
        $this->taskPriority = $taskPriority;
    }

    public function __toString()
    {
        return substr($this->content, 0, 20). '...';
    }

}
