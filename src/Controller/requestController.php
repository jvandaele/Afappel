<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Entity\Promotion;
use App\Entity\Seance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @package App\Controller
 */
class requestController extends AbstractController {

    /**
     * @Route("/request/inscription/student/{ine}/{birthdate}", name="requete.inscriptionStudent", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param null $ine
     * @param null $birthdate
     * @return JsonResponse|null
     */
    public function inscriptionEtudiant(Request $request, EntityManagerInterface $manager, $ine = null, $birthdate = null): ?JsonResponse
    {
        $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['dateNaissance' => $birthdate, 'ine' => $ine]);
        if($etudiant != null){
            $donneesRenvoyees = ['id' => $etudiant->getId()];
            return new JsonResponse($donneesRenvoyees);
        }
        return $this->generateErrorJSON("Erreur 11 : L'étudiant n'a pas été trouvé dans la base de données.");
    }

    /**
     * @Route("/request/inscription/teacher/{arpege}/{birthdate}", name="requete.inscriptionTeacher", methods={"GET"})
     * @param EntityManagerInterface $manager
     * @param null $arpege
     * @param null $birthdate
     * @return JsonResponse|null
     */
    public function inscriptionTeacher(EntityManagerInterface $manager, $arpege = null, $birthdate = null): ?JsonResponse
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['dateNaissance' => $birthdate, 'arpege' => $arpege]);
        if($professeur != null){
            $donneesRenvoyees = ['id' => $professeur->getId()];
            return new JsonResponse($donneesRenvoyees);
        }
        return $this->generateErrorJSON("Erreur 12 : L'enseignant n'a pas été trouvé dans la base de données.");
    }

    /**
     * @Route("/request/session/{id}", name="requete.creationSession", methods={"GET"})
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return JsonResponse
     */
    public function creationSession(EntityManagerInterface $manager, $id = null): JsonResponse
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $id]);
        if($professeur != null){
            $matieres = $professeur->getMatieres();
            $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
            $classes = $promotionActuelle->getClasses();
            $groupes = [];
            for($i=0;$i<count($classes);$i++){
                $groupesClasse = $classes[$i]->getGroupes();
                for($j=0;$j<count($groupesClasse);$j++){
                    $verif = true;
                    for($k=0;$k<count($groupes);$k++){
                        if($groupesClasse[$j]->getId() == $groupes[$k]->getId()){
                            $verif = false;
                            break;
                        }
                    }
                    if($verif){ $groupes[] = $groupesClasse[$j];}
                }
            }
            $matieresEnvoyees = [];
            $groupesEnvoyes = [];
            for($i=0;$i<count($matieres);$i++){
                $matieresEnvoyees[] = ['id' => $matieres[$i]->getId(), 'label' => $matieres[$i]->getNomMatiere()];
            }
            for($i=0;$i<count($groupes);$i++){
                $groupesEnvoyes[] = ['id' => $groupes[$i]->getId(), 'label' => $groupes[$i]->getNomGroupe()];
            }
            $donneesRenvoyees = ['disciplines' => $matieresEnvoyees, 'groups' => $groupesEnvoyes];
            return new JsonResponse($donneesRenvoyees);
        }
        return $this->generateErrorJSON("Erreur 12 : L'enseignant n'a pas été trouvé dans la base de données.");
    }

    /**
     * @Route("/request/group/{id}", name="requete.getGroupe", methods={"GET"})
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return null
     */
    public function getGroupe(EntityManagerInterface $manager, $id = null): ?JsonResponse
    {
        $groupe = $manager->getRepository(Groupe::class)->findOneBy(['id' => $id]);
        $etudiantsEnvoyes = [];
        if($groupe != null){
            $etudiants = $groupe->getEtudiants();
            for($i=0;$i<count($etudiants);$i++){
                $etudiantsEnvoyes[] = ['id' => $etudiants[$i]->getId(), 'prenomEtudiant' => $etudiants[$i]->getPrenomEtudiant(), 'nomEtudiant' => $etudiants[$i]->getNomEtudiant()];
            }
            $donneesRenvoyees = ['students' => $etudiantsEnvoyes];
            return new JsonResponse($donneesRenvoyees);
        }
        return $this->generateErrorJSON("Erreur 13 : Le groupe n'a pas été trouvé dans la base de données.");
    }

    /**
     * @Route("/request/call/validate", name="requete.validationAppel", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function validationAppel(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $data = $this->getDataFromJSON($request);
        if($data != null){
            $seance = new Seance();
            $date = date("d-m-Y H:i:s");
            $seance->setDate($date);
            if($data['idSeance'] != -1){
                $seance = $manager->getRepository(Seance::class)->findOneBy(['id' => $data['idSeance']]);
                if($seance == null){ return $this->generateErrorJSON("Erreur 15 : La séance n'a pas été trouvée dans la base de données."); }
                foreach($seance->getAbsences() as $absence){
                    $seance->removeAbsence($absence);
                    $manager->remove($absence);
                }
                foreach($seance->getGroupes() as $groupe){ $seance->removeGroupe($groupe); }
            }
            for($i=0;$i<count($data['groupsId']);$i++){
                $groupe = $manager->getRepository(Groupe::class)->findOneBy(['id' => $data['groupsId'][$i]]);
                if($groupe != null){ $seance->addGroupe($groupe); }
                else { return $this->generateErrorJSON("Erreur 13 : Le groupe n'a pas été trouvé dans la base de données."); }
            }
            $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $data['teacherId']]);
            if($professeur != null){ $seance->setProfesseur($professeur); }
            else{ return $this->generateErrorJSON("Erreur 12 : L'enseignant n'a pas été trouvé dans la base de données."); }
            $matiere = $manager->getRepository(Matiere::class)->findOneBy(['id' => $data['disciplineId']]);
            if($matiere != null){ $seance->setMatiere($matiere); }
            else{ return $this->generateErrorJSON("Erreur 14 : La matière n'a pas été trouvée dans la base de données."); }
            for($i=0;$i<count($data['attendances']);$i++){
                $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['id' => $data['attendances'][$i]['id']]);
                if($etudiant != null){
                    if(!$data['attendances'][$i]['presence']){
                        $absence = new Absence();
                        $etudiantAbsent = $manager->getRepository(Etudiant::class)->findOneBy(['id' => $data['attendances'][$i]['id']]);
                        $absence->setEtudiant($etudiantAbsent);
                        $absence->setJustifiee(false);
                        $absence->setJustification("");
                        $manager->persist($absence);
                        $seance->addAbsence($absence);
                    }
                }
                else{ return $this->generateErrorJSON("Erreur 11 : L'étudiant n'a pas été trouvé dans la base de données."); }
            }
            $manager->persist($seance);
            $manager->flush();
            $donneesRenvoyees = ['ok' => true];
            return new JsonResponse($donneesRenvoyees);
        }
        return $this->generateErrorJSON("Erreur 0 : Le JSON envoyé n'a pas pu être décodé.");
    }

    /**
     * @Route("/request/call/{id}", name="requete.getAppel", methods={"GET"})
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return JsonResponse
     */
    public function getDernierAppel(EntityManagerInterface $manager, $id = null): JsonResponse
    {
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $id]);
        if($professeur == null){ return $this->generateErrorJSON("Erreur 12 : L'enseignant n'a pas été trouvé dans la base de données."); }
        $seances = $professeur->getSeances();
        $seance = $seances[(count($seances)-1)];
        if($seance == null){ return $this->generateErrorJSON("Erreur 15 : La séance n'a pas été trouvée dans la base de données."); }
        $groupes = [];
        $etudiants = [];
        $etudiantsJSON = [];
        foreach($seance->getGroupes() as $groupe){
            foreach($groupe->getEtudiants() as $etudiant){ $etudiants[] = $etudiant; }
            $groupes[] = ['id' => $groupe->getId(), 'label' => $groupe->getNomGroupe()];
        }
        foreach($etudiants as $etudiant){
            $presence = true;
            foreach($seance->getAbsences() as $absence){
                if($absence->getEtudiant()->getId() == $etudiant->getId()){ $presence = false; }
            }
            $etudiantsJSON[] = [
                'id' => $etudiant->getId(),
                'prenomEtudiant' => $etudiant->getPrenomEtudiant(),
                'nomEtudiant' => $etudiant->getNomEtudiant(),
                'presence' => $presence,
            ];
        }
        $donneesRenvoyees = [
            'idSeance' => $seance->getId(),
            'idMatiere' => $seance->getMatiere()->getId(),
            'labelMatiere' => $seance->getMatiere()->getNomMatiere(),
            'idProfesseur' => $seance->getProfesseur()->getId(),
            'groups' => $groupes,
            'students' => $etudiantsJSON
        ];
        return new JsonResponse($donneesRenvoyees);
    }

    private function getDataFromJSON(Request $request): ?array
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            return $data;
        }
        return null;
    }

    private function generateErrorJSON($msg): JsonResponse
    {
        $msg = $msg . "\nSi vous constatez cette erreur, veuillez contacter l'administrateur du site web Afappel.";
        $donneesRenvoyees = ['requestError' => $msg];
        return new JsonResponse($donneesRenvoyees);
    }
}