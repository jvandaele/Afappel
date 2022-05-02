<?php

namespace App\Controller\adminControllers;

use App\Entity\Absence;
use App\Entity\Promotion;
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
* @IsGranted("ROLE_ADMIN")
*/
class absencesAdminController extends AbstractController {

    /**
     * @Route("/admin/absences", name="admin.absences.list")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showList(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
        $classesActuelles = $promotionActuelle->getClasses();
        $groupesActuels = [];
        foreach($classesActuelles as $classe){
            foreach($classe->getGroupes() as $groupe){ $groupesActuels[] = $groupe; }
        }
        $seancesActuelles = [];
        foreach($groupesActuels as $groupe){
            foreach($groupe->getSeances() as $seance){
                $verif = true;
                foreach($seancesActuelles as $seanceActuelle){
                    if($seanceActuelle->getId() == $seance->getId()){
                        $verif = false;
                        break;
                    }
                }
                if($verif){ $seancesActuelles[] = $seance; }
            }
        }
        $absencesActuelles = [];
        foreach($seancesActuelles as $seance){
            foreach($seance->getAbsences() as $absence){ $absencesActuelles[] = $absence; }
        }
        $absences = $manager->getRepository(Absence::class)->findAll();
        return new Response($twig->render('Admin/absencesAdmin/absencesAdminList.html.twig', ["absences" => $absences, "absencesActuelles" => $absencesActuelles]));
    }

    /**
     * @Route("/admin/absences/show/{id}", name="admin.absences.show")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(Environment  $twig, EntityManagerInterface $manager, $id = null): Response
    {
        $absence = $manager->getRepository(Absence::class)->findOneBy(['id' => $id]);
        if($absence != null){
            $etudiant = $absence->getEtudiant();
            $absencesEtudiant = $etudiant->getAbsences();
            $absencesMatiere = 0;
            $absencesMatiereJustifiees = 0;
            foreach($absencesEtudiant as $absenceEtudiant){
                if($absenceEtudiant->getSeance()->getMatiere()->getId() == $absence->getSeance()->getMatiere()->getId()){
                    $absencesMatiere++;
                    if($absenceEtudiant->getJustifiee()){ $absencesMatiereJustifiees++; }
                }
            }
            return new Response($twig->render('Admin/absencesAdmin/absencesAdminShow.html.twig', ["absence" => $absence, "absences" => $absencesMatiere, "absencesJustifiees" => $absencesMatiereJustifiees]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/absences/justify/{id}", name="admin.absences.justify")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @param null $id
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function justify(Environment $twig,  EntityManagerInterface $manager, $id = null): Response
    {
        $absence = $manager->getRepository(Absence::class)->findOneBy(['id' => $id]);
        if($absence != null){
            return new Response($twig->render('Admin/absencesAdmin/absencesAdminJustify.html.twig', ["absence" => $absence]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("admin/absences/justifySubmit", name="admin.absences.justifySubmit")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function justifySubmit(EntityManagerInterface $manager): RedirectResponse
    {
        $absence = $manager->getRepository(Absence::class)->findOneBy(['id' => $_POST['id']]);
        $absence->setJustifiee(true);
        $absence->setJustification($_POST['justification']);
        $manager->persist($absence);
        $manager->flush();
        return $this->redirectToRoute('admin.absences.list');
    }
}