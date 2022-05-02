<?php

namespace App\Controller\professeurControllers;

use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Entity\Promotion;
use App\Entity\Seance;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @IsGranted("ROLE_TEACHER")
 */
class absencesProfesseurController extends AbstractController
{
    /**
     * @Route("/professeur/absences", name="professeur.absences")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showListAbsences(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $matieres = $professeur->getMatieres();
        $matiere = null;
        if(!count($matieres) == 0){
            if(isset($_POST['matiere'])){ $matiere = $manager->getRepository(Matiere::class)->findOneBy(['id' => $_POST['matiere']]); }
            else{ $matiere = $matieres[0]; }
            $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
            $classesActuelles = $promotionActuelle->getClasses();
            $groupesActuels = [];
            foreach($classesActuelles as $classe){
                foreach ($classe->getGroupes() as $groupe){
                    $verif = true;
                    foreach($groupesActuels as $groupeActuel){
                        if($groupe->getId() == $groupeActuel->getId()){
                            $verif = false;
                            break;
                        }
                    }
                    if($verif){ $groupesActuels[] = $groupe; }
                }
            }
            $seancesTriees = [];
            foreach($groupesActuels as $groupe){
                foreach($groupe->getSeances() as $seance){
                    $verif = true;
                    if($seance->getMatiere()->getId() != $matiere->getId() || $seance->getProfesseur()->getId() != $professeur->getId()){ $verif = false; }
                    foreach($seancesTriees as $seanceTriee){
                        if($seanceTriee->getId() == $seance->getId()){
                            $verif = false;
                            break;
                        }
                    }
                    if($verif){ $seancesTriees[] = $seance; }
                }
            }
            $absences = [];
            foreach($seancesTriees as $seance){
                foreach($seance->getAbsences() as $absence){ $absences[] = $absence; }
            }
            $etudiants = [];
            $nbAbsences = [];
            $nbAbsencesJustifiees = [];
            foreach($absences as $absence){
                $verif = true;
                for($i = 0;$i<count($etudiants);$i++){
                    if($etudiants[$i]->getId() == $absence->getEtudiant()->getId()){
                        $nbAbsences[$i]++;
                        if($absence->getJustifiee()){ $nbAbsencesJustifiees[$i]++; }
                        $verif = false;
                        break;
                    }
                }
                if($verif){
                    $etudiants[] = $absence->getEtudiant();
                    $nbAbsences[] = 1;
                    if($absence->getJustifiee()){ $nbAbsencesJustifiees[] = 1; }
                    else{ $nbAbsencesJustifiees[] = 0; }
                }
            }
            return new Response($twig->render('Professeur/absencesProfesseurList.html.twig', ["matieres" => $matieres, "matiere" => $matiere, "etudiants" => $etudiants, "nbAbsences" => $nbAbsences, "nbAbsencesJustifiees" => $nbAbsencesJustifiees]));
        }
        return new Response($twig->render('Professeur/absencesProfesseurList.html.twig', ["matieres" => $matieres]));
    }
}