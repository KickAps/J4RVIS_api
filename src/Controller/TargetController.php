<?php

namespace App\Controller;

use App\Repository\TargetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TargetController extends AbstractController {
    public const DAILY = "D";
    public const WEEKLY = "W";
    public const MONTHLY = "M";
    public const YEARLY = "Y";

    #[Route('/target', name: 'target')]
    public function index(TargetRepository $targetRepository, Request $request, HomeController $homeController): Response {
        if($response = $homeController->check_cookie_password($request)) {
            return $response;
        }
        $targets = $targetRepository->findAll();

        return $this->render('target/index.html.twig', [
            'targets' => $targets,
        ]);
    }
}
