<?php

namespace App\Controller;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends AbstractController
{
    /**
     * @Rest\Post("/group", name="post_group")  *
     * @SWG\Post(
     *     tags={"Group"},
     *     summary="Add a new group",
     *     @SWG\Response(response="200", description="Returned when group created"),
     * )
     *
     * @param EntityManagerInterface $entityManager,
     * @return Response
     */
    #[Route('/group', name: 'create_group', methods: ["POST"])]
    public function createGroup(
        EntityManagerInterface $entityManager,
    ): Response {

        $group = new Group();
        $entityManager->persist($group);
        $entityManager->flush();

        return $this->json($group, Response::HTTP_OK);
    }



    #[Route('/group/{id}', name: 'delete_group', methods: ["DELETE"])]
    public function deleteEmptyGroup(
        Group $group = null,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$group) {
            return $this->json("Group not found", Response::HTTP_NOT_FOUND);
        }
        $members = $group->getMembers();
        if (count($members) == !0) {
            return $this->json('Group still has members and cannot be deleted.', Response::HTTP_BAD_REQUEST);
        }
        $entityManager->remove($group);
        $entityManager->flush();

        return $this->json('Group deleted successfully.', Response::HTTP_OK);
    }
}
