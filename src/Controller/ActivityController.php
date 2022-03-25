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
    #[Route('/activity', name: 'activity_list')]
    public function listAction(EntityManagerInterface $em, ActivityRepository $activityRepository): Response
    {
        $activity_list = $activityRepository->findAll();
        $activity_json = [];
        foreach ($activity_list as $activity) {
            $start = $activity->getStartedAt()->format('Y-m-d H:i:s');
            $stop = $activity->getStoppedAt() ? $activity->getStoppedAt()->format('Y-m-d H:i:s') : $start;
            $activity_json[] = [
                'id' => $activity->getId(),
                'title' => $activity->getTitle(),
                'start' => $start,
                'end' => $stop,
            ];
        }

        return $this->render('activity/index.html.twig', [
            'data' => json_encode($activity_json),
        ]);
    }

    #[Route("/activity/{id}", name: 'activity_show', methods: ['GET'])]
    public function showAction(Activity $activity, SerializerInterface $serializer)
    {
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

    #[Route("/activity/stop", name: 'activity_stop', methods: ['PUT'])]
    public function stopActivity(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ActivityRepository $activityRepository)
    {
        // GET  $request->query->get('title')
        // POST $request->request->get('title')
        // PUT  $request->get('title')

        $title = $request->get('title');

        $activity = $activityRepository->findOneBy([
            'title' => $title,
            'stoppedAt' => null
        ]);
        $activity->setStoppedAt(new \DateTimeImmutable());

        $em->persist($activity);
        $em->flush();

        return new Response('', Response::HTTP_ACCEPTED);
    }

    // TODO : Affichage des activités côté MOBILE
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
