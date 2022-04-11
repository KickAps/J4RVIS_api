<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GarminConnectController extends AbstractController {
    const COOKIES_FILE_PATH = "garmin-connect/cookies.json";

    #[Route('/garmin/connect/cookies', name: 'app_garmin_connect_cookies', methods: ['GET', 'POST'])]
    public function index(Request $request): Response {
        $cookies = $request->request->get('cookies');
        if($cookies) {
            file_put_contents(self::COOKIES_FILE_PATH, $cookies);
            return $this->redirectToRoute('target');
        }
        return $this->render('garmin_connect/index.html.twig');
    }
}
