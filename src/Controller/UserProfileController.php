<?php

namespace App\Controller;

use App\Form\ChangeEmailFormType;
use App\Form\ChangePseudoFormType;
use App\Form\EditPhotoFormType;
use App\Form\RegistrationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'user_')]
class UserProfileController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $bgc = "red";

        $emailForm=$this->createForm(ChangeEmailFormType::class);
        $emailForm->handleRequest($request);

        if($emailForm->isSubmitted() && $emailForm->isValid()) {
            $newEmail = $emailForm->get('email')->getData();
            $this->getUser()->setEmail($newEmail);

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Adresse Email modifiée avec succès');

            return $this->redirectToRoute('user_profile');
        }

        $pseudoForm=$this->createForm(ChangePseudoFormType::class);
        $pseudoForm->handleRequest($request);

        if($pseudoForm->isSubmitted() && $pseudoForm->isValid()) {
            $newPseudo = $pseudoForm->get('pseudonym')->getData();
            $this->getUser()->setPseudonym($newPseudo);

            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Pseudo modifié avec succès');

            return $this->redirectToRoute('user_profile');
        }
        return $this->render('user_profile/profile_index.html.twig', [
            'controller_name' => 'UserProfileController',
            'emailForm' => $emailForm->createView(),
            'pseudoForm' => $pseudoForm->createView(),
            'bgc' => $bgc
        ]);
    }

    #[Route('/editer-photo/', name: 'edit_photo')]
    #[isGranted('ROLE_USER')]
    public function editPhoto(Request $request, ManagerRegistry $doctrine): Response
    {

        $form=$this->createForm(EditPhotoFormType::class);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $photo = $form->get('photo')->getData();

            if(
                $this->getUser()->getPhoto() != null &&
                file_exists($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() )
            ){
                unlink($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() );
            }


            /*Génération nom*/
            do{
                $newFileName = md5( random_bytes(100) ) . '.' . $photo->guessExtension();
            } while (file_exists($this->getParameter('app.user.photo.directory') .$newFileName));

            $this->getUser()->setPhoto($newFileName);

            $em = $doctrine->getManager();
            $em->flush();

            $photo -> move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName,
            );

            $this->addFlash('success', 'Photo de profil modifiée avec succès');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user_profile/edit_photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
