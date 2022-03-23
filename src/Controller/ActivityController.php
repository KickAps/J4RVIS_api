<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ActivityRepository;
use App\Entity\Activity;

class ActivityController extends AbstractController
{
    #[Route("/activity/{id}", name: 'activity_show', methods: ['GET'])]
    public function showAction(Activity $activity, SerializerInterface $serializer)
    {
        // $activity = new Activity();
        // $activity
        //     ->setTitle('Mon premier article')
        //     ->setStartedAt(new \DateTimeImmutable())
        //     ->setStoppedAt(new \DateTimeImmutable())
        // ;
        $data = $serializer->serialize($activity, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/activity/start", name: 'activity_start', methods: ['POST'])]
    public function startActivity(Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $data = $request->getContent();
        $activity = $serializer->deserialize($data, Activity::class, 'json');
        $activity->setStartedAt(new \DateTimeImmutable());

        $em->persist($activity);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    #[Route("/activity/{id}/stop", name: 'activity_stop', methods: ['PUT'])]
    public function stopActivity(Activity $activity, Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $activity->setStoppedAt(new \DateTimeImmutable());

        $em->persist($activity);
        $em->flush();

        return new Response('', Response::HTTP_ACCEPTED);
    }

    // #[Route("/articles", name: 'article_list', methods: ['GET'])]
    // public function listAction(SerializerInterface $serializer, ArticleRepository $articleRepository)
    // {
    //     $articles = $articleRepository->findAll();

    //     $data = $serializer->serialize($articles, 'json');

    //     $response = new Response($data);
    //     $response->headers->set('Content-Type', 'application/json');

    //     return $response;
    // }
}
