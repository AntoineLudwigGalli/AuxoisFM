<?php

namespace App\Controller;

use App\Entity\RadioShow;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/", name: "main_")]
class MainController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/', name: 'home')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $bgc = "blue";

        $showsRepo = $doctrine->getRepository(RadioShow::class);
        $shows = $showsRepo->findBy([], ['name' => 'ASC']);



//        $date = $shows->getStartDate();
//        if( $date > date('Y-m-d')) {
//            $shows->setStartDate($date->add(new \DateInterval($shows->getTimeInterval())));
//        }

        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
            'bgc' => $bgc,
            'shows' => $shows
        ]);
    }

    #[Route('/news', name: 'news')]
    public function news(): Response
    {
        $bgc = "red";

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
