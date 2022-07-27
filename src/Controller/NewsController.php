<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/news', name: 'news_')]
class NewsController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function newsList(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
//        Couleur du background
        $bgc = "rgba(122, 220, 241, 0.4)";

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $doctrine->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');
        $articles = $paginator->paginate($query, //Requête créée juste avant
            $requestedPage, // Page qu'on souhaite voir
            8, // Nombre d'articles à afficher par page
        );
        return $this->render('news/list.html.twig', [
            'controller_name' => 'NewsController',
            'articles' => $articles,
            'bgc' => $bgc
        ]);
    }

    #[Route('/nouvel-article/', name: 'new_article')]
    #[isGranted('ROLE_ADMIN')]
    public function newArticle(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response {
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setPublicationDate(new \DateTime())->setSlug($slugger->slug($article->getTitle())->lower());

            $photo = $form->get('coverPicture')->getData();


            /*Génération nom*/
            do{
                $newFileName = md5( random_bytes(50) ) . '.' . $photo->guessExtension();
            } while (file_exists($this->getParameter('article.photo.directory') .$newFileName));

            $article->setCoverPicture($newFileName);

            $em = $doctrine->getManager();
            $em->persist($article);
            $em->flush();

            $photo -> move(
                $this->getParameter('article.photo.directory'),
                $newFileName,
            );


            $this->addFlash('success', 'Article publié avec succès');

            return $this->redirectToRoute('news_article_view', [
                'id' => $article->getId(),
                'slug' => $article->getSlug(),
            ]);
        }

        return $this->render('news/new_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/{slug}/', name: 'article_view')]
    #[ParamConverter('article', options: ['mapping' => ['id' => 'id', 'slug' => 'slug']])]
    public function articleView(Article $article, Request $request, ManagerRegistry $doctrine): Response {
        //        Couleur du background
        $bgc = "rgba(122, 220, 241, 0.4)";

        if (!$this->getUser()) {
            return $this->render('news/article_view.html.twig', [
                'article' => $article,]);
        }
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPublicationDate(new \DateTime())->setAuthor($this->getUser())->setArticle($article);;

            $em = $doctrine->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire publié avec succès');

            unset($comment);
            unset($form);

            $comment = new Comment;
            $form = $this->createForm(CommentFormType::class, $comment);
        }
        return $this->render('news/article_view.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
            'bgc' => $bgc
        ]);
    }

    #[Route("/article/suppression/{id}/", name: 'article_delete', priority: 10)]
    #[isGranted("ROLE_ADMIN")]
    public function publicationDelete(Article $article, Request $request, ManagerRegistry $doctrine): Response {
        $csrfToken = $request->query->get('csrf_token', '');

        if (!$this->isCsrfTokenValid('news_article_delete_' . $article->getId(), $csrfToken)) {
            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer');
        } else {

            $em = $doctrine->getManager();
            $em->remove($article);
            $em->flush();

            $this->addFlash('success', 'Article supprimé avec succès');
        }

        return $this->redirectToRoute('news_list');
    }

    #[Route("/article/modifier/{id}/", name: 'article_edit', priority: 10)]
    #[isGranted("ROLE_ADMIN")]
    public function publicationEdit(Article $article, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response {

        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setSlug($slugger->slug($article->getTitle())->lower());
            $photo = $form->get('coverPicture')->getData();

            if(
                $article->getCoverPicture() != null &&
                file_exists($this->getParameter('article.photo.directory') . $article->getCoverPicture())
            ){
                unlink($this->getParameter('article.photo.directory') . $article->getCoverPicture() );
            }

            /*Génération nom*/
            do{
                $newFileName = md5( random_bytes(50) ) . '.' . $photo->guessExtension();
            } while (file_exists($this->getParameter('article.photo.directory') .$newFileName));

            $article->setCoverPicture($newFileName);

            $em = $doctrine->getManager();
            $em->persist($article);
            $em->flush();

            $photo -> move(
                $this->getParameter('article.photo.directory'),
                $newFileName,
            );


            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('news_list', [
                'id' => $article->getId(),
                'slug' => $article->getSlug(),]);
        }

        return $this->render('news/article_edit.html.twig', [
            'form' => $form->createView(),]);
    }

    #[Route('/commentaire/suppression/{id}/', name: "comment_delete")]
    #[IsGranted('ROLE_ADMIN')]
    public function commentDelete(Comment $comment, Request $request, ManagerRegistry $doctrine): Response {

        if (!$this->isCsrfTokenValid('comment_delete_' . $comment->getId(), $request->query->get('csrf_token'))) {
            $this->addFlash('error', 'Token sécurité invalide, veuillez ré-essayer');
        } else {

            $em = $doctrine->getManager();
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès');
        }
        return $this->redirectToRoute('news_article_view', [
            'id' => $comment->getArticle()->getId(),
            'slug' => $comment->getArticle()->getSlug()]);
    }

    #[Route('/recherche/', name: 'search')]
    public function search(Request $request, PaginatorInterface $paginator, ManagerRegistry $doctrine): Response
    {

        // Récupération du numéro de la page demandée dans l'url (s'il n'existe pas, 1 sera pris à la place)
        $requestedPage = $request->query->getInt('page', 1);

        // Si la page demandée est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        // On récupère la recherche de l'utilisateur depuis l'url ($_GET['q'])
        $search = $request->query->get('s', '');

        // Récupération du manager général des entités
        $em = $doctrine->getManager();

        // Création d'une requête permettant de récupérer les articles pour la page actuelle, dont le titre ou le contenu contient la recherche de l'utilisateur
        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.content LIKE :search ORDER BY a.publicationDate DESC')
            ->setParameters([
                'search' => '%' . $search . '%',
            ])
        ;

        // Récupération des articles
        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );

        // Appel de la vue en lui envoyant les articles à afficher
        return $this->render('news/list_search.html.twig', [
            'articles' => $articles,
        ]);
    }
}
