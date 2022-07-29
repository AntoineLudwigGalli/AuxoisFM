<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\RadioShow;
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

        // Afin d'avoir une boucle affiche tout de même les émissions après 52 itérations, on réinitialise la date de début à la date de diffusion une fois celle-ci passée
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
        $articlesRepo = $doctrine->getRepository(Article::class);
        $articles = $articlesRepo->findBy(
            [],
            ['publicationDate' => 'DESC'],
            $this->getParameter('articles.last_article_displayed_on_home'),
        );

        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
            'bgc' => $bgc,
            'shows' => $shows,
            'articles' => $articles
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
