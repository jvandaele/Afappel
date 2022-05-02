<?php

namespace App\Controller\adminControllers;

use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Promotion;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class etudiantsAdminController extends AbstractController {

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/etudiants", name="admin.etudiants.list")
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
        $etudiantsActuels = [];
        foreach($groupesActuels as $groupe){
            foreach($groupe->getEtudiants() as $etudiant){ $etudiantsActuels[] = $etudiant; }
        }
        $etudiants = $manager->getRepository(Etudiant::class)->findAll();
        return new Response($twig->render('Admin/etudiantsAdmin/etudiantsAdminList.html.twig', ["etudiants" => $etudiants, "etudiantsActuels" => $etudiantsActuels]));
    }

    /**
     * @Route("/admin/etudiants/show/{id}", name="admin.etudiants.show")
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
        $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['id' => $id]);
        if($etudiant != null){
            return new Response($twig->render('Admin/etudiantsAdmin/etudiantsAdminShow.html.twig', ["etudiant" => $etudiant]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/etudiants/create", name="admin.etudiants.create")
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
        return new Response($twig->render('Admin/etudiantsAdmin/etudiantsAdminCreate.html.twig', ['groupes' => $groupesActuels]));
    }

    /**
     * @Route("/admin/etudiants/createSubmit", name="admin.etudiants.createSubmit")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function createSubmit(EntityManagerInterface $manager): RedirectResponse
    {
        $nbGroupes = $_POST['nbGroupes'];
        $groupes = [];
        for($i = 0;$i<$nbGroupes;$i++){
            $inputName = 'groupe' . $i;
            if(isset($_POST[$inputName])){
                $inputIdName = 'idGroupe' . $i;
                $groupes[] = $manager->getRepository(Groupe::class)->findOneBy(['id' => $_POST[$inputIdName]]);
            }
        }
        $morceauxDate = explode("-", $_POST['dateNaissanceEtudiant']);
        $etudiant = new Etudiant();
        $etudiant->setPrenomEtudiant($_POST['prenomEtudiant']);
        $etudiant->setNomEtudiant($_POST['nomEtudiant']);
        $etudiant->setIne(($_POST['ine']));
        $etudiant->setEmailEtudiant($_POST['emailEtudiant']);
        $etudiant->setDateNaissance($morceauxDate[2] . "-" . $morceauxDate[1] . "-" . $morceauxDate[0]);
        foreach($groupes as $groupe){ $etudiant->addGroupe($groupe); }
        $manager->persist($etudiant);
        $manager->flush();
        $newUser = new User();
        $newUser->setUsername($_POST['username']);
        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $etudiant->getIne()));
        $newUser->setRoles(["ROLE_TEACHER"]);
        $newUser->setType("Professeur");
        $newUser->setTypeId($etudiant->getId());
        $manager->persist($newUser);
        $manager->flush();
        return $this->redirectToRoute("admin.professeurs.list");
    }
}