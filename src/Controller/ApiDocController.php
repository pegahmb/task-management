<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiDocController extends AbstractController

{
    #[Route('/swagger',  methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirect('/api/doc.json');
    }
}
