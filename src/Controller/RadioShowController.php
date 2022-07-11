<?php

namespace App\Controller;

use App\Entity\RadioShow;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/show", name: "show_")]
class RadioShowController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function show_list(ManagerRegistry $doctrine): Response
    {
        $showsRepo = $doctrine->getRepository(RadioShow::class);
        $shows = $showsRepo->findBy([], ['name' => 'ASC']);

        return $this->render('radio_show/list.html.twig', [
            'controller_name' => 'RadioShowController',
            'shows' => $shows
        ]);
    }

    #[Route('/supprimer/{id}/', name: 'delete', priority: 10)]
    public function showDelete(RadioShow $radioShow, Request $request, ManagerRegistry $doctrine): Response
    {
//        Token CSRF
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('show_delete' . $radioShow->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');

        } else {
            // Suppression de l'émission en BDD
            $em = $doctrine->getManager();
            $em->remove($radioShow);
            $em->flush();

            // Message flash de succès
            $this->addFlash('success', "L'émission a été supprimée avec succès !");
        }
        // Redirection vers la page qui liste les events
        return $this->redirectToRoute('show_list');
    }
}
