<?php

namespace App\Controller;

use App\Builder\UserBuilder;
use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/user', name: 'add_user', methods: ["POST"])]
    public function addUser(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $userBuilder = new UserBuilder(json_decode($request->getContent(), true));

        if (count($userBuilder->getErrors())) {
            return $this->json(join(", ", $userBuilder->getErrors()), Response::HTTP_BAD_REQUEST);
        }

        $user = $userBuilder->getUser();

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_OK);
    }

    #[Route('/user/{id}', name: 'delete_user', methods: ["DELETE"])]
    public function deleteUser(
        User $user = null,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$user) {
            return $this->json("User not found", Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json('User deleted successfully.', Response::HTTP_OK);
    }

    #[Route('/user/{userId}/group/{groupId}', name: 'assign_user_to_group', methods: ["POST"])]
    public function assignUserToGroup(
        int $userId,
        int $groupId,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $group = $entityManager->getRepository(Group::class)->find($groupId);

        if (!$user) {
            return $this->json("User not found", Response::HTTP_NOT_FOUND);
        }
        if (!$group) {
            return $this->json("Group not found", Response::HTTP_NOT_FOUND);
        }
        if ($group->getMembers()->contains($user)) {
            return $this->json('User is already a member of the group.', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $group->addMember($user);
        $entityManager->flush();

        return $this->json('User assigned to the group successfully.', Response::HTTP_OK);
    }

    #[Route('/user/{userId}/group/{groupId}', name: 'remove_user_from_group', methods: ["DELETE"])]
    public function removeUserFromGroup(
        int $userId,
        int $groupId,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $group = $entityManager->getRepository(Group::class)->find($groupId);

        if (!$user) {
            return $this->json("User not found", Response::HTTP_NOT_FOUND);
        }
        if (!$group) {
            return $this->json("Group not found", Response::HTTP_NOT_FOUND);
        }

        if (!$group->getMembers()->contains($user)) {
            return $this->json('User is not a member of the group.', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $group->removeMember($user);
        $entityManager->flush();

        return $this->json('User removed from the group successfully.', Response::HTTP_OK);
    }
}
