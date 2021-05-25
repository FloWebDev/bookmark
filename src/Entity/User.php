<?php

namespace App\Entity;

use App\Constant\Constant;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`app_user`")
 * @UniqueEntity("username", message="Identifiant déjà utilisé")
 * @UniqueEntity("email", message="Email déjà utilisé")
 * @UniqueEntity("slug", message="Slug déjà utilisé")
 */
class User implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Zà-źÀ-Ź0-9]+$/",
     *     match=true,
     *     message="L'identifiant est constitué de lettres et chiffres uniquement"
     * )
     */
    private $username;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=256, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $role;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $connected_at;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $wallpaper;

    /**
     * @ORM\OneToMany(targetEntity=Page::class, mappedBy="user", orphanRemoval=true)
     * @ORM\OrderBy({"z" = "ASC", "created_at" = "ASC"})
     */
    private $pages;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->slug       = str_replace('.', '', uniqid('BM', true));
        $this->pages      = new ArrayCollection();
        $this->wallpaper  = Constant::WALLPAPERS['Business'];
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles[] = $this->role;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        if (!is_null($password)) {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = mb_strtolower($email);

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getConnectedAt(): ?\DateTimeInterface
    {
        return $this->connected_at;
    }

    public function setConnectedAt(?\DateTimeInterface $connected_at): self
    {
        $this->connected_at = $connected_at;

        return $this;
    }

    public function getWallpaper(): ?string
    {
        return $this->wallpaper;
    }

    public function setWallpaper(?string $wallpaper): self
    {
        $this->wallpaper = $wallpaper;

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setUser($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getUser() === $this) {
                $page->setUser(null);
            }
        }

        return $this;
    }

    /**
     * La méthode sera appelée lors de la comparaison des utilisateurs afin de vérifier s'il nécessaire de forcer
     * l'utilisateur à se reconnecter.
     * L'implémentation de cette méthode permet d'éviter les déconnexions intempestives (voir exemples cités ci-dessous).
     *
     * @link https://stackoverflow.com/questions/63924686/symfony5-update-user-profile-cause-logout-when-edit-form-submitted
     * @link https://github.com/symfony/symfony/issues/33418
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->getId() !== $user->getId()) {
            return false;
        }

        return true;
    }
}
