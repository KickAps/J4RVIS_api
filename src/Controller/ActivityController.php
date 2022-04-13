<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Calendar;
use App\Repository\ActivityRepository;
use App\Repository\CalendarRepository;
use App\Utils\Config;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends AbstractController {
    const SOMMEIL = "Sommeil";

    #[Route('/activity', name: 'activity_list')]
    public function listAction(Request $request, CalendarRepository $calendarRepository, HomeController $homeController, Config $config): Response {
        if($response = $homeController->check_cookie_password($request)) {
            return $response;
        }

        $calendar_list = $calendarRepository->findAll();
        $calendar_json = [];
        foreach($calendar_list as $calendar) {
            $title = $calendar->getActivity()->getTitle();
            $start = $calendar->getStartedAt()->format('Y-m-d H:i:s');
            if($calendar->getStoppedAt()) {
                $stop = $calendar->getStoppedAt()->format('Y-m-d H:i:s');
            } else {
                $stop = (new DateTimeImmutable())->format('Y-m-d H:i:s');
                $title .= " (En cours)";
            }
            $calendar_json[] = [
                'id' => $calendar->getId(),
                'title' => $title,
                'start' => $start,
                'end' => $stop,
            ];
        }

        return $this->render('activity/index.html.twig', [
            'data' => json_encode($calendar_json),
            'last_data_sleep_refresh' => $config->getConfigByKey('last_data_sleep_refresh')
        ]);
    }

    #[Route("/activity/{id}", name: 'activity_show', methods: ['GET'])]
    public function showAction(Activity $activity, SerializerInterface $serializer): Response {
        // TODO : pas utilisée
        $data = $serializer->serialize($activity, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    #[Route("/activity/start", name: 'activity_start', methods: ['POST'])]
    public function startActivity(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ActivityRepository $activityRepository): Response {
        $data = $request->getContent();
        $activity = $serializer->deserialize($data, Activity::class, 'json');

        if($a = $activityRepository->findOneBy(['title' => $activity->getTitle()])) {
            $activity = $a;
        } else {
            $em->persist($activity);
        }

        $calendar = new Calendar();
        $calendar->setActivity($activity);
        $calendar->setStartedAt(new DateTimeImmutable());

        $em->persist($calendar);

        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    #[Route("/activity/stop", name: 'activity_stop', methods: ['PUT'])]
    public function stopActivity(Request $request, EntityManagerInterface $em, ActivityRepository $activityRepository, CalendarRepository $calendarRepository): Response {
        // GET  $request->query->get('title')
        // POST $request->request->get('title')
        // PUT  $request->get('title')

        $title = $request->get('title');
        $activity = $activityRepository->findOneBy([
            'title' => $title
        ]);
        $calendar = $calendarRepository->findOneBy([
            'activity' => $activity,
            'stoppedAt' => null
        ]);

        $start = $calendar->getStartedAt();
        $stop = new DateTimeImmutable();

        if($stop->getTimestamp() - $start->getTimestamp() < 30 * 60) {
            $stop = $start->add(new DateInterval('PT30M'));
        }

        $calendar->setStoppedAt($stop);

        $em->persist($calendar);
        $em->flush();

        return new Response('', Response::HTTP_ACCEPTED);
    }

    #[Route("/activity/sleep", name: 'activity_sleep', methods: ['POST'])]
    public function sleepActivity(Request $request, EntityManagerInterface $em, ActivityRepository $activityRepository, CalendarRepository $calendarRepository): Response {
        $data = $request->getContent();
        $data_json = json_decode($data, true);

        if($a = $activityRepository->findOneBy(['title' => self::SOMMEIL])) {
            $activity = $a;
        } else {
            $activity = new Activity();
            $activity->setTitle(self::SOMMEIL);
            $em->persist($activity);
        }

        $startedAt = (new DateTimeImmutable())->setTimestamp($data_json['startedAt']);
        $stoppedAt = (new DateTimeImmutable())->setTimestamp($data_json['stoppedAt']);
        $calendar = $calendarRepository->findOneBy([
            'activity' => $activity,
            'startedAt' => $startedAt,
            'stoppedAt' => $stoppedAt,
        ]);
        if($calendar) {
            return new Response('', Response::HTTP_ACCEPTED);
        }

        $calendar = new Calendar();
        $calendar->setActivity($activity);
        $calendar->setStartedAt($startedAt);
        $calendar->setStoppedAt($stoppedAt);

        $em->persist($calendar);

        $em->flush();

        return new Response('', Response::HTTP_CREATED);
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
