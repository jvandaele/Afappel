<?php

namespace App\Controller\professeurControllers;

use App\Entity\Professeur;
use App\Entity\Seance;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @IsGranted("ROLE_TEACHER")
 */
class seancesProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/seances", name="professeur.seances")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showListSeances(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $seances = $manager->getRepository(Seance::class)->findBy(['professeur' => $professeur]);
        return new Response($twig->render('Professeur/seancesProfesseurList.html.twig', ['seances' => $seances]));
    }

    /**
     * @Route("/professeur/seance/{id}", name="professeur.seanceDetails")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showDetailSeance(Environment $twig,  EntityManagerInterface $manager, $id = null): Response
    {
        $seance = $manager->getRepository(Seance::class)->findOneBy(['id' => $id]);
        if($seance->getProfesseur()->getId() != $this->getUser()->getTypeId()){
            return new Response($twig->render('Professeur/seanceDetails.html.twig', ['seance' => $seance, 'nope' => true]));
        }
        return new Response($twig->render('Professeur/seanceDetails.html.twig', ['seance' => $seance, 'nope' => false]));
    }
}