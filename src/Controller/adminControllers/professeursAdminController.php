<?php

namespace App\Controller\adminControllers;

use App\Entity\Professeur;
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
class professeursAdminController extends AbstractController {

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/professeurs", name="admin.professeurs.list")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showList(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $professeurs = $manager->getRepository(Professeur::class)->findAll();
        return new Response($twig->render('Admin/professeursAdmin/professeursAdminList.html.twig', ["professeurs" => $professeurs]));
    }

    /**
     * @Route("/admin/professeurs/show/{id}", name="admin.professeurs.show")
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
        $professeur = $manager->getRepository(Professeur::class)->findOneBy(['id' => $id]);
        if($professeur != null){
            return new Response($twig->render('Admin/professeursAdmin/professeursAdminShow.html.twig', ["professeur" => $professeur]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/professeurs/create", name="admin.professeurs.create")
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Environment $twig): Response
    {
        return new Response($twig->render('Admin/professeursAdmin/professeursAdminCreate.html.twig'));
    }

    /**
     * @Route("/admin/professeurs/createSubmit", name="admin.professeurs.createSubmit")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function createSubmit(EntityManagerInterface $manager): RedirectResponse
    {
        $morceauxDate = explode("-", $_POST['dateNaissanceProfesseur']);
        $professeur = new Professeur();
        $professeur->setPrenomProfesseur($_POST['prenomProfesseur']);
        $professeur->setNomProfesseur($_POST['nomProfesseur']);
        $professeur->setArpege(($_POST['arpege']));
        $professeur->setEmailProfesseur($_POST['emailProfesseur']);
        $professeur->setDateNaissance($morceauxDate[2] . "-" . $morceauxDate[1] . "-" . $morceauxDate[0]);
        $manager->persist($professeur);
        $manager->flush();
        $newUser = new User();
        $newUser->setUsername($_POST['username']);
        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $professeur->getArpege()));
        $newUser->setRoles(["ROLE_TEACHER"]);
        $newUser->setType("Professeur");
        $newUser->setTypeId($professeur->getId());
        $manager->persist($newUser);
        $manager->flush();
        return $this->redirectToRoute("admin.professeurs.list");
    }
}