<?php

namespace App\Controller;

use App\Repository\SerieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SerieController extends AbstractController
{
    #[Route('/series/{page}', name: 'app_serie_list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index(SerieRepository $serieRepository, int $page): Response
    {

        $nbByPage = 10;
        $offset = ($page - 1) * $nbByPage;

        $criteria = ['status' => 'RETURNING', 'Genders' => 'gore'];
        $nbSeries = $serieRepository->count($criteria);
        $nbPagesMax = ceil($nbSeries / $nbByPage);

        /*
        //$seriesList = $serieRepository->findAll();
        $seriesList = $serieRepository->findBy(
            $criteria,
            ['nbVote' => 'DESC'],
            $nbByPage,
            $offset
        );
        */

        //$seriesList = $serieRepository->findBestSeriesWithSpecificGenre(['Gore','horror'], '%non%');
        //$seriesList = $serieRepository->getBestSeriesInDQL();
        $seriesList = $serieRepository->getBestSeriesInRawSQL(); // Explosera sans le dd
        dd($seriesList);

        return $this->render('serie/index.html.twig',
            [
                'series'=> $seriesList,
                'page' => $page,
                'nbPagesMax' => $nbPagesMax
            ]
        );
    }
}
