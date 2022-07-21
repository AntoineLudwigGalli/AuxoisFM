<?php

namespace App\Controller;

use App\Entity\RadioShow;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    #[Route('/json/shows', name: 'app_json_shows')]
    public function jsonAction(ManagerRegistry $doctrine): Response
    {
        // On récupère dans la BDD les infos des émissions et on les convertit en page JSON
        $showsDataRepository = $doctrine->getRepository(RadioShow::class);
        $showsData = $showsDataRepository->findAll();

        foreach ($showsData as $showData){
            $name = $showData->getName();
            $animator = $showData->getAnimator();
            $webpageLink = $showData->getwebPageLink();
            $logo = $showData->getLogo();
            $podcasts = $showData->getPodcasts();
            $startDate = $showData->getStartDate();
            $timeInterval = $showData->getTimeInterval();
            $showTime = $showData->getShowTime();
            $showDuration = $showData->getShowDuration();
            $broadcastDay = $showData->getBroadcastDay();

            /**
             * Mon tableau de données pour afficher sur le calendrier
             */
            $data[] = [
                "name" => $name,
                "animator" => $animator,
                "webpageLink" => $webpageLink,
                "logo" => $logo,
                "podcasts" => $podcasts,
                "startDate" => $startDate,
                "timeInterval" => $timeInterval,
                "showTime" => $showTime,
                "showDuration" => $showDuration,
                "broadcastDay" => $broadcastDay,

            ];
        }
        return new JsonResponse($data);

    }
}
