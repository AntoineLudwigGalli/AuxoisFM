<?php

namespace App\Controller;

use App\Entity\RadioShow;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/show", name: "show_")]
class RadioShowController extends AbstractController
{
    #[Route('/list', name: 'show_list')]
    public function show_list(ManagerRegistry $doctrine): Response
    {
        $showsRepo = $doctrine->getRepository(RadioShow::class);
        $shows = $showsRepo->findBy([], ['name' => 'ASC']);

        return $this->render('radio_show/show_list.html.twig', [
            'controller_name' => 'RadioShowController',
            'shows' => $shows
        ]);
    }
}
