<?php

namespace App\Entity;

use App\Repository\GroupRepository;

use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "groups")]
    private Collection $members;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $user): void
    {
        if (!$this->members->contains($user)) {
            $this->members[] = $user;
        }
    }

    public function removeMember(User $user): void
    {
        $this->members->removeElement($user);
    }
}
