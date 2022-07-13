<?php

namespace App\Controller;

use App\Entity\RadioShow;
use App\Form\RadioShowCreationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/nouvelle-emission/', name: 'new_show')]
    #[isGranted('ROLE_ANIMATOR')]
    public function newPublication(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response {
        $show = new RadioShow();

        $form = $this->createForm(RadioShowCreationFormType::class, $show);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $show->setAnimator($this->getUser())->setSlug($slugger->slug($show->getName())->lower());
            $logo = $form->get('logo')->getData();

            if(
                $show->getLogo() != null
            ){


                /*Génération nom*/
                do {
                    $newFileName = md5(random_bytes(50)) . '.' . $logo->guessExtension();
                } while (file_exists($this->getParameter('show.logo.directory') . $newFileName));

                $show->setLogo($newFileName);

                $logo -> move(
                    $this->getParameter('show.logo.directory'),
                    $newFileName,
                );
            }
            
            $em = $doctrine->getManager();
            $em->persist($show);
            $em->flush();


            $this->addFlash('success', 'Emission créée avec succès');

            return $this->redirectToRoute('main_home', [
                'id' => $show->getId(),
                'slug' => $show->getSlug(),]);
        }

        return $this->render('radio_show/new_show.html.twig', [
            'form' => $form->createView(),


        ]);
    }
}
