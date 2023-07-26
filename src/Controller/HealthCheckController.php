<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Error;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckController extends AbstractController
{

  #[Route('/health', name: 'health_check', methods: ["GET"])]
  public function healthCheck(Connection $connection): Response
  {
    try {
      // Try to execute a simple query to check if the database is connected
      $connection->executeQuery('SELECT 1');
      return $this->json("DB connected succesfully");
    } catch (Error $ex) {
    }

    return $this->json("Error connecting db", Response::HTTP_INTERNAL_SERVER_ERROR);
  }
}
