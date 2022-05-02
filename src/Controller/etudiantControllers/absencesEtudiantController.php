<?php

namespace App\Controller\etudiantControllers;

use App\Entity\Absence;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Matiere;
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
 * @IsGranted("ROLE_STUDENT")
 */
class absencesEtudiantController extends AbstractController {

    /**
     * @Route("/etudiant/absences", name="etudiant.absences")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showListAbsences(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['id' => $this->getUser()->getTypeId()]);
        $absences = $manager->getRepository(Absence::class)->findBy(['etudiant' => $etudiant]);
        $groupes = $etudiant->getGroupes();
        $matieres = [];
        foreach($absences as $absence){
            $seance = $absence->getSeance();
            $matiere = $seance->getMatiere();
            $verif = true;
            foreach($matieres as $matiereAbsence){
                if($matiere == $matiereAbsence){
                    $verif = false;
                }
            }
            if($verif){
                $matieres[] = $matiere;
            }
        }
        $absencesJustifiees = $manager->getRepository(Absence::class)->findBy(['etudiant' => $etudiant, 'justifiee' => true]);
        $absencesInjustifiees = $manager->getRepository(Absence::class)->findBy(['etudiant' => $etudiant, 'justifiee' => false]);
        if(isset($_POST['groupe']) && isset($_POST['matiere'])) {
            if($_POST['groupe'] != -1 || $_POST['matiere'] != -1){
                $matiere = $manager->getRepository(Matiere::class)->findOneBy(['id' => $_POST['matiere']]);
                $groupe = $manager->getRepository(Groupe::class)->findOneBy(['id' => $_POST['groupe']]);
                if ($_POST['groupe'] != -1 && $_POST['matiere'] == -1) {
                    $absencesJustifiees = $this->triageAbsencesGroupe($groupe, $absencesJustifiees);
                    $absencesInjustifiees = $this->triageAbsencesGroupe($groupe, $absencesInjustifiees);
                } else if ($_POST['matiere'] != -1 && $_POST['groupe'] == -1) {
                    $absencesJustifiees = $this->triageAbsencesMatiere($matiere, $absencesJustifiees);
                    $absencesInjustifiees = $this->triageAbsencesMatiere($matiere, $absencesInjustifiees);
                } else {
                    $absencesJustifiees = $this->triageAbsencesMatiereGroupe($matiere, $groupe, $absencesJustifiees);
                    $absencesInjustifiees = $this->triageAbsencesMatiereGroupe($matiere, $groupe, $absencesInjustifiees);
                }
                if($matiere == null){ $matiere = -1; }
                else{ $matiere = $matiere->getId(); }
                if($groupe == null){ $groupe = -1; }
                else{ $groupe = $groupe->getId(); }
                return new Response($twig->render('Etudiant/absencesEtudiantList.html.twig', [
                    'absencesInjustifiees' => $absencesInjustifiees,
                    'absencesJustifiees' => $absencesJustifiees,
                    'groupes' => $groupes,
                    'groupeSelectionne' => $groupe,
                    'matieres' => $matieres,
                    'matiereSelectionnee' => $matiere
                ]));
            }
        }
        return new Response($twig->render('Etudiant/absencesEtudiantList.html.twig', [
            'absencesInjustifiees' => $absencesInjustifiees,
            'absencesJustifiees' => $absencesJustifiees,
            'groupes' => $groupes,
            'groupeSelectionne' => -1,
            'matieres' => $matieres,
            'matiereSelectionnee' => -1
        ]));
    }

    private function triageAbsencesGroupe(Groupe $groupe, $absences): array
    {
        $absencesTriees = [];
        foreach($absences as $absence){
            $seance = $absence->getSeance();
            $groupesSeance = $seance->getGroupes();
            foreach($groupesSeance as $groupeSeance){
                if($groupeSeance == $groupe){ $absencesTriees[] = $absence; }
            }
        }
        return $absencesTriees;
    }

    private function triageAbsencesMatiere(Matiere $matiere, $absences): array
    {
        $absencesTriees = [];
        foreach($absences as $absence){
            $seance = $absence->getSeance();
            if($seance->getMatiere() == $matiere){ $absencesTriees[] = $absence; }
        }
        return $absencesTriees;
    }

    private function triageAbsencesMatiereGroupe(Matiere $matiere, Groupe $groupe, $absences): array
    {
        $absencesTriees = [];
        foreach($absences as $absence){
            $seance = $absence->getSeance();
            $groupesSeance = $seance->getGroupes();
            foreach($groupesSeance as $groupeSeance){
                if(($seance->getMatiere() == $matiere) && ($groupeSeance == $groupe)){ $absencesTriees[] = $absence; }
            }
        }
        return $absencesTriees;
    }
}