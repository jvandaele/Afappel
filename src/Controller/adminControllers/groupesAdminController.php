<?php

namespace App\Controller\adminControllers;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Promotion;
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
 * @IsGranted("ROLE_ADMIN")
 */
class groupesAdminController extends AbstractController {

    /**
     * @Route("/admin/groupes", name="admin.groupes.list")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showList(Environment $twig, EntityManagerInterface $manager): Response
    {
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
        $groupes = $manager->getRepository(Groupe::class)->findAll();
        return new Response($twig->render('Admin/groupesAdmin/groupesAdminList.html.twig', ["groupesActuels" => $groupesActuels, "groupes" => $groupes]));
    }

    /**
     * @Route("/admin/groupes/show/{id}", name="admin.groupes.show")
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
        $groupe = $manager->getRepository(Groupe::class)->findOneBy(['id' => $id]);
        if($groupe != null){
            return new Response($twig->render('Admin/groupesAdmin/groupesAdminShow.html.twig', ["groupe" => $groupe]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/groupes/create", name="admin.groupes.create")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Environment $twig, EntityManagerInterface $manager): Response
    {
        $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
        $classesActuelles = $promotionActuelle->getClasses();
        return new Response($twig->render('Admin/groupesAdmin/groupesAdminCreate.html.twig', ['classes' => $classesActuelles]));
    }

    /**
     * @Route("/admin/groupes/createSubmit", name="admin.groupes.createSubmit")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createSubmit(Environment $twig, EntityManagerInterface  $manager): Response
    {
        $nbClasses = $_POST['nbClasses'];
        $classes = [];
        for($i = 0;$i<$nbClasses;$i++){
            $inputName = 'classe' . $i;
            if(isset($_POST[$inputName])){
                $inputIdName = 'idClasse' . $i;
                $classes[] = $manager->getRepository(Classe::class)->findOneBy(['id' => $_POST[$inputIdName]]);
            }
        }
        $groupes = [];
        foreach($classes as $classe){
            foreach($classe->getGroupes() as $groupe) { $groupes[] = $groupe; }
        }
        $etudiants = [];
        foreach($groupes as $groupe){
            foreach($groupe->getEtudiants() as $etudiant){ $etudiants[] = $etudiant; }
        }
        return new Response($twig->render('Admin/groupesAdmin/groupesAdminCreate2.html.twig', ['classes' => $classes, 'etudiants' => $etudiants, 'nomGroupe' => $_POST['nomGroupe']]));
    }

    /**
     * @Route("/admin/groupes/createFinal", name="admin.groupes.createFinal")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function createFinal(EntityManagerInterface  $manager): Response
    {
        $nbEtudiants = $_POST['nbEtudiants'];
        $etudiants = [];
        for($i = 0;$i<$nbEtudiants;$i++){
            $inputName = 'etudiant' . $i;
            if(isset($_POST[$inputName])){
                $inputIdName = 'idEtudiant' . $i;
                $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['id' => $_POST[$inputIdName]]);
                $etudiants[] = $etudiant;
            }
        }
        $nbClasses = $_POST['nbClasses'];
        $classes = [];
        for($i = 0;$i<$nbClasses;$i++){
            $inputName = 'classe' . $i;
            $classes[] = $manager->getRepository(Classe::class)->findOneBy(['id' => $_POST[$inputName]]);
        }
        $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
        $groupe = new Groupe();
        $groupe->setNomGroupe($_POST['nomGroupe']);
        $groupe->setAnnee($promotionActuelle->getAnnee());
        foreach($classes as $classe){ $groupe->addClasse($classe); }
        foreach($etudiants as $etudiant){ $groupe->addEtudiant($etudiant); }
        $manager->persist($groupe);
        $manager->flush();
        return $this->redirectToRoute('admin.groupes.list');
    }
}