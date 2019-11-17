<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *                  "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *                  "normalization_context"={
 *                                          "groups"={"get"}
 *                  }
 *           },
 *          "put"={
 *                  "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *                  "denormalization_context"={
 *                                            "groups"={"put"}
 *                  },
 *                  "normalization_context"={
 *                                          "groups"={"get"}
 *                  }
 *           },
 *          "put-reset-password"={
 *                  "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *                  "method"="PUT",
 *                  "path"="/users/{id}/reset-password",
 *                  "controller"=ResetPasswordAction::class,
 *                  "denormalization_context"={
 *                                            "groups"={"put-reset-password"}
 *                  },
 *                  "validation_groups"={"put-reset-password"}
 *           }
 *     },
 *     collectionOperations={
 *                          "post"={
 *                                  "denormalization_context"={
 *                                                           "groups"={"post"}
 *                                  },
 *                                  "normalization_context"={
 *                                                           "groups"={"get"}
 *                                  },
 *                                  "validation_groups"={"post"}
 *                          }
 *
 *     },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, groups={"post","put"})
 * @UniqueEntity(fields={"email"}, groups={"post","put"})
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN= 'ROLE_SUPER_ADMIN';

    const DEFAULT_ROLES = [self::ROLE_USER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-owner"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post","get-task-with-author","get-project-with-author"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Length(min="4", groups={"post", "put"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post"})
     * @Groups({"post"})
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long ond contain at last one digit, one upper case letter and one lower case letter ",
     *     groups={"post"}
     * )
     */
    private $password;

    /**
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Expression(
     *     "this.getPassword()===this.getRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"post"}
     * )
     */
    private $retypedPassword;

    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Groups({"put-reset-password"})
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long ond contain at last one digit, one upper case letter and one lower case letter ",
     *     groups={"put-reset-password"}
     * )
     */
    private $newPassword;

    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Groups({"put-reset-password"})
     * @Assert\Expression(
     *     "this.getNewPassword()===this.getNewRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"put-reset-password"}
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "put", "get-admin","get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Email(groups={"post","put"})
     * @Assert\Length(min="6", groups={"post","put"})
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="author")
     * @Groups({"get-owner"})
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="author")
     * @Groups({"get-owner"})
     */
    private $projects;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin","get-owner"})
     */
    private $roles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProfilePicture")
     * @ORM\JoinTable()
     * @ApiSubresource()
     * @Groups({"post","get-owner"})
     */
    private $profilepictures;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled= false;
        $this->confirmationToken= null;
        $this->profilepictures= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTasks() : Collection
    {
        return $this->tasks;
    }

    /**
     * @return Collection
     */
    public function getProjects() : Collection
    {
        return $this->projects;
    }

    public function getRoles() : array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles=$roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getProfilepictures() : Collection
    {
        return $this->profilepictures;
    }

    public function addImage(ProfilePicture $profilepicture)
    {
        $this->profilepictures->add($profilepicture);
    }

    public function removeImage(ProfilePicture $profilepicture)
    {
        $this->profilepictures->removeElement($profilepicture);
    }

    public function __toString() : string
    {
        return $this->username;
    }

}
