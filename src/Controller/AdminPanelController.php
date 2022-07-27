<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\RadioShow;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Form\RadioShowCreationFormType;
use App\Form\RoleFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\Writer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SplTempFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route("/admin", name: "admin_panel_")]
#[IsGranted('ROLE_ADMIN')]
class AdminPanelController extends AbstractController
{
// Page d'accueil du panel admin
    #[Route('', name: 'home')]
    public function homeAdmin(ManagerRegistry $doctrine, PaginatorInterface $paginator): Response
    {
    // Requêtes de toutes les entrées dans les tables des entités Articles, Emissions, Commentaires et Podcasts pour en afficher le nombre dans les boutons de l'index admin
        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ');

        $articles = $paginator->paginate($query, 55);

        $query = $em->createQuery('SELECT rs FROM App\Entity\RadioShow rs');

        $shows = $paginator->paginate($query, 55);

        $query = $em->createQuery('SELECT c FROM App\Entity\Comment c');

        $comments = $paginator->paginate($query, 55);

        $query = $em->createQuery('SELECT p FROM App\Entity\Podcast p');

        $podcasts = $paginator->paginate($query, 55);

        $query = $em->createQuery('SELECT u FROM App\Entity\User u');

        $users = $paginator->paginate($query, 55);


        return $this->render('admin_panel/home.html.twig', [
                "articles" => $articles,
                "shows" => $shows,
                "comments" => $comments,
                "podcasts" => $podcasts,
                'users' => $users,
            ]
        );
    }

//    Gestion des émissions

    #[Route('/liste-des-emissions', name: 'shows_list')]
    public function eventList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        // On récupère dans l'URL la donnée GET page (si elle n'existe pas, la valeur retournée par défaut sera la page 1)
        $requestedPage = $request->query->getInt('page', 1);

        // Si le numéro de page demandé dans l'URL est inférieur à 1, erreur 404
        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }
        // Récupération du manager des entités
        $em = $doctrine->getManager();

        // Création d'une requête qui servira au paginator pour récupérer les émissions de la page courante
        $query = $em->createQuery('SELECT rs FROM App\Entity\RadioShow rs JOIN rs.animator us ORDER BY rs.name ASC');

        //On stocke dans $shows les 10 shows de la page demandée dans l'URL
        $shows = $paginator->paginate(
            $query, // Requête de selection des émissions en BDD
            $requestedPage, // Numéro de la page dont on veut les émissions
            5); // Nombre d'émissions par page

        return $this->render('admin_panel/shows_list.html.twig', [
            'shows' => $shows,]); // On envoie les émissions récupérées à la vue
    }

    #[Route('/suppression-d\'une-emission/{id}/', name: 'show_delete', priority: 10)]
    public function showDelete(RadioShow $show, Request $request, ManagerRegistry $doctrine): Response
    {

//        Token
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('admin_show_delete' . $show->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');

        }
        else {
            // Suppression de l'émission en BDD
            $em = $doctrine->getManager();
            $em->remove($show);
            $em->flush();

            // Message flash de succès
            $this->addFlash('success', "L'émission a été supprimée avec succès !");
        }
        // Redirection vers la page qui liste les events
        return $this->redirectToRoute('admin_panel_shows_list');
    }

    #[Route('/modification-d\'une-emission/{id}/', name: 'show_edit', priority: 10)]
    public function showEdit(RadioShow $radioShow, Request $request, ManagerRegistry $doctrine): Response
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
            return $this->redirectToRoute('admin_panel_shows_list', [
                'id' => $radioShow->getId(),
            ]);
        }

        return $this->render('admin_panel/show_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rechercher-une-emission/', name: 'shows_search')]
    public function searchShow(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('search', '');

        $em = $doctrine->getManager();

        $query =
            $em->createQuery('SELECT rs FROM App\Entity\RadioShow rs WHERE rs.name LIKE :search OR rs.broadcastDay LIKE :search')
                ->setParameters(['search' => '%' . $search . '%']);

        $shows = $paginator->paginate($query,
            $requestedPage,
            5
        );

        return $this->render('admin_panel/shows_search.html.twig', [
            'shows' => $shows,
        ]);
    }

    // Export CSV des Emissions avec le bundle CSV League
    #[Route('/liste-des-emissions/export', name: 'shows_list_export')]
    public function showsListExport(ManagerRegistry $doctrine): Response
    {

        // Création des en-têtes des colonnes du fichier de tableur
        $header = ['Titre', 'Animateur', 'Jour de diffusion', 'Date de la prochaine émission', 'Heure de début', 'Durée de l\'émission'];

        // On va chercher tous les utilisateurs présents en bdd
        $showRepo = $doctrine->getRepository(RadioShow::class);

        $shows = $showRepo->findAll();
        $broadcastDaysNumber = ['0', '1', '2', '3', '4', '5', '6'];
        $broadcastDays = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        // Pour chaque utilisateur, on affiche ses données dans une ligne. On en profite pour rendre plus facilement lisible les données
        foreach ($shows as $show) {
            $arrayShows[] = [
                $show->getName(),
                $show->getAnimator()->getPseudonym(),
                str_replace($broadcastDaysNumber, $broadcastDays, implode(' ,', $show->getBroadcastDay())),
                $show->getStartDate()->format('d/m/Y'),
                $show->getShowTime()->format('H:i'),
                $show->getShowDuration()->format('H:i'),
            ];
        }
dump($arrayShows);
        $writer = Writer::createFromFileObject(new SplTempFileObject()); //the CSV file will be created using a temporary File
        $writer->setDelimiter("\t"); //the delimiter will be the tab character
        $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($header);
        $writer->insertAll($arrayShows ?? []);


        // Provide a name for your file with extension
        $filename = 'shows.csv';

        // Return a response with a specific content
        $response = new Response($writer);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;

    }

//    Gestion des utilisateurs

    #[Route('/liste-des-utilisateurs', name: 'users_list')]
    public function usersList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.id DESC');
        $users = $paginator->paginate($query, $requestedPage, 55);


        return $this->render('admin_panel/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/suppression-d\'un-utilisateur/{id}/', name: 'user_delete', priority: 10)]
    public function userDelete(User $user, Request $request, ManagerRegistry $doctrine): Response
    {
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('user_delete' . $user->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');

        }
        else {
            // Suppression de l'utilisateur en BDD
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();

            // Message flash de succès
            $this->addFlash('success', "L'utilisateur a été supprimé avec succès !");
        }
        // Redirection vers la page qui liste les utilisateurs
        return $this->redirectToRoute('admin_panel_users_list');
    }

    #[Route('/changer-le-role/{id}', name: 'change_user_role')]
    public function test(User $user, ManagerRegistry $doctrine, Request $request): Response
    {
        $csrfToken = $request->query->get('csrf_token', '');
dump($user->getRoles()[0]);
        if (!$this->isCsrfTokenValid('user_change_roles' . $user->getId(), $csrfToken)) {

            $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');
            return $this->redirectToRoute('admin_panel_users_list');
        }
        else {
            if ($user->getRoles()[0] == "ROLE_ANIMATOR") {
                $user->setRoles(["ROLE_USER"]);
                $this->addFlash('success', 'L\'utilisateur est désormais visiteur');
            }
            else if ($user->getRoles()[0] == "ROLE_USER") {
                $user->setRoles(["ROLE_ANIMATOR"]);
                $this->addFlash('success', 'L\'utilisateur est désormais animateur');
            }
            else {
                $this->addFlash('error', 'Les droits de cet utilisateurs ne peuvent pas être modifiés');
            }


            $em = $doctrine->getManager();
            $em->flush();


            return $this->redirectToRoute('admin_panel_users_list');
        }
    }

    #[Route('/rechercher-un-utilisateur/', name: 'users_search')]
    public function searchUser(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('search', '');

        $em = $doctrine->getManager();

        $query =
            $em->createQuery('SELECT u FROM App\Entity\User u WHERE u.firstname LIKE :search OR u.lastname LIKE :search OR u.email LIKE :search OR u.pseudonym LIKE :search')
                ->setParameters(['search' => '%' . $search . '%']);

        $users = $paginator->paginate($query,
            $requestedPage,
            5
        );

        return $this->render('admin_panel/users_search.html.twig', [
            'users' => $users,
            ]);
    }

    // Export CSV des Users avec le bundle CSV League
    #[Route('/liste-des-utilisateurs/export', name: 'users_list_export')]
    public function usersListExport(ManagerRegistry $doctrine): Response
    {

        // Création des en-têtes des colonnes du fichier de tableur
        $header = ['Pseudo', 'Nom', 'Prénom', 'Rôle', 'E-mail'];

        // On va chercher tous les utilisateurs présents en bdd
        $userRepo = $doctrine->getRepository(User::class);

        $users = $userRepo->findAll();

        // Pour chaque utilisateur, on affiche ses données dans une ligne. On en profite pour rendre plus facilement lisible les données
        foreach ($users as $user) {
            $arrayUsers[] = [$user->getPseudonym(), $user->getFirstname(), $user->getLastname(),
                in_array('ROLE_ADMIN', $user->getRoles()) ? 'Administrateur' : (in_array('ROLE_ANIMATOR', $user->getRoles()) ? 'Animateur' : 'Visiteur'), $user->getEmail()
            ];
        }

        $writer = Writer::createFromFileObject(new SplTempFileObject()); //the CSV file will be created using a temporary File
        $writer->setDelimiter("\t"); //the delimiter will be the tab character
        $writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
        $writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output
        $writer->insertOne($header);
        $writer->insertAll($arrayUsers ?? []);


        // Provide a name for your file with extension
        $filename = 'users.csv';

        // Return a response with a specific content
        $response = new Response($writer);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;

    }

//    Gestion des articles

    #[Route('/liste-des-articles', name: 'articles_list')]
    public function articlesList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');
        $articles = $paginator->paginate($query, $requestedPage, 5);

        return $this->render('admin_panel/articles_list.html.twig', [
            'articles' => $articles,
        ]);
    }
    #[Route('/modification-d\'un-article/{id}/', name: 'article_edit', priority: 10)]
    public function articleEdit(Article $article, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setSlug($slugger->slug($article->getTitle())->lower());
            $photo = $form->get('coverPicture')->getData();

            if (
                $article->getCoverPicture() != null &&
                file_exists($this->getParameter('article.photo.directory') . $article->getCoverPicture())
            ) {
                unlink($this->getParameter('article.photo.directory') . $article->getCoverPicture());
            }

            /*Génération nom*/
            do {
                $newFileName = md5(random_bytes(50)) . '.' . $photo->guessExtension();
            } while (file_exists($this->getParameter('article.photo.directory') . $newFileName));

            $article->setCoverPicture($newFileName);

            $em = $doctrine->getManager();
            $em->persist($article);
            $em->flush();

            $photo->move(
                $this->getParameter('article.photo.directory'),
                $newFileName,
            );


            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin_panel_articles_list');
        }

        return $this->render('admin_panel/article_edit.html.twig', [
            'form' => $form->createView(),]);
    }

    #[Route("/article/suppression/{id}/", name: 'article_delete', priority: 10)]
    #[isGranted("ROLE_ADMIN")]
    public function publicationDelete(Article $article, Request $request, ManagerRegistry $doctrine): Response {
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('admin_article_delete' . $article->getId(), $csrfToken)) {
            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer');
        } else {

            $em = $doctrine->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('admin_panel_articles_list');
    }

    #[Route('/rechercher-un-article/', name: 'articles_search')]
    public function searchArticle(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('search', '');

        $em = $doctrine->getManager();

        $query =
            $em->createQuery('SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.publicationDate LIKE :search')
                ->setParameters(['search' => '%' . $search . '%']);

        $articles = $paginator->paginate($query,
            $requestedPage,
            5
        );

        return $this->render('admin_panel/articles_search.html.twig', [
            'articles' => $articles,
        ]);
    }

}
