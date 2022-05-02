<?php

namespace App\Controller\professeurControllers;

use App\Entity\Matiere;
use App\Entity\Professeur;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @IsGranted("ROLE_TEACHER")
 */
class matieresProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/matieres", name="professeur.matieres")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showListMatieres(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $matieresEnseignees = $professeur->getMatieres();
        $matieres = $manager->getRepository(Matiere::class)->findAll();
        return new Response($twig->render('Professeur/matieresProfesseurList.html.twig', ['matieresEnseignees' => $matieresEnseignees, 'matieres' => $matieres]));
    }

    /**
     * @Route("/professeur/matiere/add", name="professeur.matieres.add")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function addMatiere(EntityManagerInterface $manager): RedirectResponse
    {
        $matiere = $manager->getRepository(Matiere::class)->findOneBy(['id' => $_POST['matieres']]);
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $professeur->addMatiere($matiere);
        $manager->persist($professeur);
        $manager->flush();
        return $this->redirectToRoute('professeur.matieres');
    }

    /**
     * @Route("/professeur/matiere/remove/{id}", name="professeur.matieres.remove")
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return RedirectResponse
     */
    public function removeMatiere(EntityManagerInterface $manager, $id = null): RedirectResponse
    {
        $matiere = $manager->getRepository(Matiere::class)->findOneBy(['id' => $id]);
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $professeur->removeMatiere($matiere);
        $manager->persist($professeur);
        $manager->flush();
        return $this->redirectToRoute('professeur.matieres');
    }
}