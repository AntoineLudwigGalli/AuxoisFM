<?php

namespace App\Controller;

use App\Entity\RadioShow;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/", name: "main_")]
class MainController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $bgc = "#c95f5f";

        $showsRepo = $doctrine->getRepository(RadioShow::class);
        $shows = $showsRepo->findBy([], ['name' => 'ASC']);

        // Afin d'avoir une boucle qui n'affiche plus les émissions après 52 itérations, on réinitialise la date de début à la date de diffusion de l'avant dernière émission
        foreach ($shows as $show) {
            if ($show->getTimeInterval() != 0) {
                $date = $show->getStartDate();
                $addOneInterval = strtotime('+' . $show->getTimeInterval() . ' days', date_timestamp_get($date));
                $oneInterval = $addOneInterval - date_timestamp_get($date);
                $intervalNumber = ceil((time() - date_timestamp_get($date)) / $oneInterval);

                if (date_timestamp_get($date) <= time()) {

                    $show->setStartDate(new \DateTime(($show->getStartDate()->modify('+' . $show->getTimeInterval() * $intervalNumber . ' day'))->format('Y-m-d')));

                    $em = $doctrine->getManager();
                    $em->flush();

                }
            }
        }

        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
            'bgc' => $bgc,
            'shows' => $shows,

        ]);
    }

    #[Route('/news', name: 'news')]
    public function news(): Response
    {
        $bgc = "rgba(201, 95, 95, 0.8)";

        return $this->render('main/news.html.twig', [
            'controller_name' => 'MainController',
            'bgc' => $bgc,
        ]);
    }

    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        $bgc = "blue";



        return $this->render('main/test.html.twig', [
            'controller_name' => 'MainController',
            'bgc' => $bgc,
        ]);
    }
}
