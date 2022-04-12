<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    const COOKIE_HASH = "$2y$10$7ZyMPl905Ik/mGVxYu1V4OcLvrckzlHNEAQjLqeRrrg8/xATooCAu";
    const PASSWORD_HASH = "$2y$10$8MXHXSp3y9wCVNQYIIUdS.PXLKIGXgPU.Tk.f1slVMKF1bZovbQIu";

    #[Route("/", name: "home", methods: ["GET", "POST"])]
    public function index(Request $request): Response {
        if(password_verify($request->cookies->get("password"), self::COOKIE_HASH)) {
            return $this->redirectToRoute("activity_list");
        }

        $password = $request->request->get("password");

        if(password_verify($password, self::PASSWORD_HASH)) {
            // Redirection
            $route = $request->request->get('route') ?? "activity_list";
            $response = $this->redirectToRoute($route);
            $response->headers->setCookie(Cookie::create("password", "valid"));
            return $response;
        }

        return $this->render("home/index.html.twig", [
            'hide_navbar' => true,
            'route' => $request->query->get('route') ?? "activity_list"
        ]);
    }

    public function check_cookie_password(Request $request, string $route) {
        if(!password_verify($request->cookies->get("password"), self::COOKIE_HASH)) {
            return $this->redirectToRoute("home", ['route' => $route]);
        }
    }
}
