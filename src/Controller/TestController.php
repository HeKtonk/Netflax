<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test_persist', name: 'app_test_persist')]
    public function test_persist(EntityManagerInterface $emi): Response
    {

        $serie = new Serie();
        $serie->setName('Buffy contre les vampires')
            ->setOverview('Buffy se bat contre les forces du mal...')
            ->setStatus('ENDED')
            ->setGenders('Comedy, Fantastic, Horror')
            ->setFirstAirDate(new \DateTime('1997-03-10'))
            ->setLastAirDate(new \DateTime('2003-05-20'))
            ->setDateCreated(new \DateTime());

        $emi->persist($serie);
        // Envoie tout ce qu'il a et se vide après
        $emi->flush();

        return new Response("L'entité est en base (normalement)");
    }

    #[Route('/test_update/{id}', name: 'app_test_update', requirements: ['id' => '\d+'])]
    public function test_update(EntityManagerInterface $emi, int $id, SerieRepository $serieRepository): Response
    {

        $serie = $serieRepository->find($id);
        $serie->setOverview('Buffy en a fini des démons');
        $serie->setDateModified(new \DateTime());

        // Ici le persist n'est pas obligatoire
        //$emi->persist($serie);
        $emi->flush();

        return new Response("L'entité est à jour en base (normalement)");
    }
}
