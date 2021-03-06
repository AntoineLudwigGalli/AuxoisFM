<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\DynamicContent;
use App\Entity\Podcast;
use App\Entity\RadioShow;
use App\Entity\ShowWebpageOptions;
use App\Form\DynamicContentFormType;
use App\Form\PodcastFormType;
use App\Form\RadioShowCreationFormType;
use App\Form\ShowWebpageOptionsFormType;
use App\Recaptcha\RecaptchaValidator;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route("/show", name: "show_")]
class RadioShowController extends AbstractController
{
    #[Route('/', name: 'list')]
    #[IsGranted('ROLE_ANIMATOR')]
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
    #[IsGranted('ROLE_ANIMATOR')]
    public function showDelete(RadioShow $radioShow, Request $request, ManagerRegistry $doctrine): Response
    {
//        Token CSRF
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('show_delete' . $radioShow->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');

        }
        else {
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

    #[Route('/modification-d\'une-emission/{id}/', name: 'edit', priority: 10)]
    public function publicationEdit(RadioShow $radioShow, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(RadioShowCreationFormType::class, $radioShow);

        //instanciation d\'un formulaire
        $form->handleRequest($request);

        //Verif du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $logo = $form->get('logo')->getData();

            if ($logo != null) {

                    if (file_exists($this->getParameter('show.logo.directory') . $radioShow->getLogo())) {
                        unlink($this->getParameter('show.logo.directory') . $radioShow->getLogo());
                    }

                    /*Génération nom*/
                    do {
                        $newFileName = md5(random_bytes(50)) . '.' . $logo->guessExtension();
                    } while (file_exists($this->getParameter('show.logo.directory') . $newFileName));

                    $radioShow->setLogo($newFileName);

                    $logo->move(
                        $this->getParameter('show.logo.directory'),
                        $newFileName,
                    );
                }
                // Sauvegarde des données modifiées en BDD
                $em = $doctrine->getManager();
                $em->flush();

                // Message flash de succès
                $this->addFlash('success', 'Emission modifiée avec succès !');

                // Redirection vers la liste des émissions contenant maintenant l'émission modifiée
                return $this->redirectToRoute('show_list', [
                    'id' => $radioShow->getId(),
                ]);
            }

        return $this->render('radio_show/show_edit.html.twig', ['form' => $form->createView(),]);

    }

    #[Route('/nouvelle-emission/', name: 'new_show')]
    #[isGranted('ROLE_ANIMATOR')]
    public function newRadioShow(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, UrlGeneratorInterface $router, RecaptchaValidator $recaptcha): Response
    {
        $show = new RadioShow();
        $webpageOptions = new ShowWebpageOptions();

        $form = $this->createForm(RadioShowCreationFormType::class, $show);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération de la réponse envoyée par le captcha dans le formulaire

            $recaptchaResponse = $request->request->get('g-recaptcha-response', null);

            // Si le captcha n'est pas valide, on crée une nouvelle erreur dans le formulaire (ce qui l'empêchera de créer l'article et affichera l'erreur)

            if($recaptchaResponse == null || !$recaptcha->verify( $recaptchaResponse, $request->server->get('REMOTE_ADDR') )){

                // Ajout d'une nouvelle erreur manuellement dans le formulaire
                $form->addError(new FormError('Le Captcha doit être validé !'));
            }

            $webpageOptions->setBackgroundColor("#ffffff");
            $webpageOptions->setTextColor('#000000');

            $em = $doctrine->getManager();
            $em->persist($webpageOptions);
            $em->flush();

            $webpageOptions->setWebpage($show);

            $show->setAnimator($this->getUser())->setSlug($slugger->slug($show->getName())->lower());

            $showSlug = $show->getSlug();
            $webpageOptionsId = $webpageOptions->getId();
            $showWebpageURL = $router->generate('show_webpage', [
                'slug' => $showSlug,
                'id' => $webpageOptionsId
            ]);
            $show->setwebPageLink($showWebpageURL);

            $logo = $form->get('logo')->getData();

            if (
                $show->getLogo() != null
            ) {

                /*Génération nom*/
                do {
                    $newFileName = md5(random_bytes(50)) . '.' . $logo->guessExtension();
                } while (file_exists($this->getParameter('show.logo.directory') . $newFileName));

                $show->setLogo($newFileName);

                $logo->move(
                    $this->getParameter('show.logo.directory'),
                    $newFileName,
                );


            }
            $article = new Article();

            copy(
                $this->getParameter('show.logo.directory') . $show->getLogo(),
                $this->getParameter('article.photo.directory') . $show->getLogo(),
            );

            if ($show->getLogo() != null) {
                $articleLogo = $show->getLogo();
            }
            else {
                $articleLogo = "default_logo.jpeg";
            }

            $article
                ->setCoverPicture($articleLogo)
                ->setTitle($show->getName() . " : la nouvelle émission qui débarque sur Auxois FM !")
                ->setContent("La nouvelle émission " . $show->getName() . " arrive sur les ondes d'Auxois FM à partir du " . $show->getStartDate()->format('d/m/Y') . " à " .
                    $show->getShowTime()->format
                    ('H:i') . " ! Découvrez là dès maintenant en cliquant ici : <a href='" .
                    $show->getwebPageLink() . "' class='btn btn-primary'>Découvrir l'émission</a>")
                ->setCategory('Emission de radio')
                ->setPublicationDate(new \DateTime())->setSlug($slugger->slug($article->getTitle())->lower());

            $em = $doctrine->getManager();
            $em->persist($show);
            $em->persist($article);
            $em->flush();


            $this->addFlash('success', 'Emission créée avec succès');

            return $this->redirectToRoute('show_webpage', [
                'slug' => $show->getSlug(),
                'id' => $webpageOptions->getId(),
            ]);
        }

        return $this->render('radio_show/new_show.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}/nouveau-podcast', name: 'new_podcast')]
    #[IsGranted('ROLE_ANIMATOR')]
    #[ParamConverter('show', options: ['mapping' => ['slug' => 'slug']])]
    public function newPodcast(ManagerRegistry $doctrine, Request $request, RadioShow $show, SluggerInterface $slugger): Response
    {

        $podcast = new Podcast();

        $form = $this->createForm(PodcastFormType::class, $podcast);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $podcast->setRadioShow($show);

            $article = new Article();

            if ($podcast->getRadioShow()->getLogo() != null) {
                $articleLogo = $podcast->getRadioShow()->getLogo();
            }
            else {
                $articleLogo = "default_logo.jpeg";
            }

            $article
                ->setCoverPicture($articleLogo)
                ->setTitle("Un nouveau podcast de l'émission " . $podcast->getRadioShow()->getName() . " est en ligne !")
                ->setContent("Un nouveau podcast de l'émission " . $podcast->getRadioShow()->getName() . " est en ligne ! Il est à écouter sur la page de l'émission cliquant ici : <a href='" .
                    $podcast->getRadioShow()->getwebPageLink() . "' class='btn btn-primary'>Ecouter le podcast</a>"
                )
                ->setCategory('Podcast')
                ->setPublicationDate(new \DateTime())->setSlug($slugger->slug($article->getTitle())->lower());

            $audio = $form->get('podcastLink')->getData();

            if ($audio != null) {

                if (file_exists($this->getParameter('podcast.audio.directory') . $audio)) {
                    unlink($this->getParameter('podcast.audio.directory') . $audio);
                }


                /*Génération nom*/

                $newFileName = $podcast->getRadioShow()->getName() .'_'. $podcast->getBroadcastDate()->format("Y-m-d") . '.' . $audio->guessExtension();

                $podcast->setPodcastLink($newFileName);

                $audio->move(
                    $this->getParameter('podcast.audio.directory').$podcast->getRadioShow()->getName(),
                    $newFileName,
                );

                //Limite à 3 podcasts par page, ensuite on détruit le fichier dans le dossier
                $directory = $this->getParameter('podcast.audio.directory').$podcast->getRadioShow()->getName();
                $scanned_directory = array_diff(scandir($directory), array('..', '.'));
                rsort($scanned_directory);

                if(count($scanned_directory)>3){
                    unlink($directory."/".$scanned_directory[2]);
                }
            }

            $em = $doctrine->getManager();
            $em->persist($podcast);
            $em->persist($article);
            $em->flush();

            $showOptionsRepo = $doctrine->getRepository(ShowWebpageOptions::class);
            $options = $showOptionsRepo->findOneBy(["webpage" => $show->getId()]);



            $this->addFlash('success', 'Le contenu a bien été modifié !');

            return $this->redirectToRoute("show_webpage", [
                'show' => $show,
                'slug' => $show->getSlug(),
                'id' => $options->getId(),
            ]);

        }

        return $this->render('radio_show/new_podcast.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer-podcast/{id}/', name: 'delete_podcast', priority: 10)]
    #[IsGranted('ROLE_ANIMATOR')]
    #[ParamConverter('podcast', options: ['mapping' => ['id' => 'id']])]
    public function podcastDelete(Podcast $podcast, Request $request, ManagerRegistry $doctrine): Response
    {

//        Token CSRF
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('podcast_delete' . $podcast->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');

        }
        else {
            // Suppression de l'émission en BDD
            $em = $doctrine->getManager();
            $em->remove($podcast);
            $em->flush();


            // Message flash de succès
            $this->addFlash('success', "Le podcast a été supprimé avec succès !");
        }

        $showsRepo = $doctrine->getRepository(RadioShow::class);
        $show = $showsRepo->findOneBy(['name' => $podcast->getRadioShow()->getName()]);
        $showOptionsRepo = $doctrine->getRepository(ShowWebpageOptions::class);
        $options = $showOptionsRepo->findOneBy(["webpage" => $show->getId()]);

        return $this->redirectToRoute("show_webpage", [
            'slug' => $show->getSlug(),
            'id' => $options->getId(),
        ]);
    }

    #[Route('/{slug}/{id}/', name: 'webpage')]
    #[ParamConverter('show', options: ['mapping' => ['slug' => 'slug']])]
    #[ParamConverter('options', options: ['mapping' => ['id' => 'id']])]
    public function showWebPage(RadioShow $show, ManagerRegistry $doctrine, Request $request, ShowWebpageOptions $options): Response
    {

        $podcastsRepo = $doctrine->getRepository(Podcast::class);
        $podcasts = $podcastsRepo->findBy(['radioShow' => $show->getId()]);

        $optionsForm = $this->createForm(ShowWebpageOptionsFormType::class, $options);

        $optionsForm->handleRequest($request);

        if ($optionsForm->isSubmitted() && $optionsForm->isValid()) {


            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Les options de la page ont bien été modifiées !');

            return $this->redirectToRoute('show_webpage', [
                'slug' => $show->getSlug(),
                'id' => $options->getId(),
            ]);
        }

        $bgc = $options->getBackgroundColor();
        $textColor = $options->getTextColor();


        return $this->render("radio_show/show_webpage.html.twig", [
            'optionsForm' => $optionsForm->createView(),
            'show' => $show,
            'options' => $options,
            'controller_name' => 'RadioShowController',
            'podcasts' => $podcasts,
            'bgc' => $bgc,
            'textColor' => $textColor,

        ]);
    }

    #[Route('/contenu-dynamique/modifier/{slug}/{title}', name: 'dynamic_content_edit', requirements: ["title" => "[a-z0-9_-]{2,50}"])]
    #[IsGranted('ROLE_ANIMATOR')]
    #[ParamConverter('show', options: ['mapping' => ['slug' => 'slug']])]
    public function dynamicContentEdit(ManagerRegistry $doctrine, Request $request, $title, RadioShow $show): Response
    {
        //On va chercher par nom (qui sert de clé) le dynamic content correspondant
        $dynamicContentRepo = $doctrine->getRepository(DynamicContent::class);

        $currentDynamicContent = $dynamicContentRepo->findOneByTitle($title);

        $em = $doctrine->getManager();

        // Si le contenu est vide, on en crée un avec le nom passé dans la fonction twig
        if (empty($currentDynamicContent)) {
            $currentDynamicContent = new DynamicContent();
            $currentDynamicContent->setTitle($title);
            $em->persist($currentDynamicContent);
        }

        // Sinon, on modifie le contenu existant par le nouveau contenu
        $form = $this->createForm(DynamicContentFormType::class, $currentDynamicContent);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $showOptionsRepo = $doctrine->getRepository(ShowWebpageOptions::class);
            $options = $showOptionsRepo->findOneBy(["webpage" => $show->getId()]);


            $em = $doctrine->getManager();
            $em->flush();

            $this->addFlash('success', 'Le contenu a bien été modifié !');

            return $this->redirectToRoute("show_webpage", [
                'show' => $show,
                'slug' => $show->getSlug(),
                'id' => $options->getId(),
            ]);

        }

        return $this->render('radio_show/dynamic_content_edit.html.twig', [
            'form' => $form->createView(),
            'show' => $show,
        ]);
    }

}
