<?php

namespace App\Controller\adminControllers;

use App\Entity\Classe;
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
class classesAdminController extends AbstractController {

    /**
     * @Route("/admin/classes", name="admin.classes.list")
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
        $classesActuelles = $manager->getRepository(Classe::class)->findBy(['promotion' => $promotionActuelle]);
        $classes = $manager->getRepository(Classe::class)->findAll();
        return new Response($twig->render('Admin/classesAdmin/classesAdminList.html.twig', ["classes" => $classes, "classesActuelles" => $classesActuelles]));
    }

    /**
     * @Route("/admin/classes/show/{id}", name="admin.classes.show")
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
        $classe = $manager->getRepository(Classe::class)->findOneBy(['id' => $id]);
        if($classe != null){
            return new Response($twig->render('Admin/classesAdmin/classesAdminShow.html.twig', ["classe" => $classe]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/classes/create", name="admin.classes.create")
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Environment $twig): Response
    {
        return new Response($twig->render('Admin/classesAdmin/classesAdminCreate.html.twig'));
    }

    /**
     * @Route("/admin/classes/createSubmit", name="admin.classes.createSubmit")
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function createSubmit(EntityManagerInterface $manager): RedirectResponse
    {
        $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
        $classe = new Classe();
        $classe->setNomClasse($_POST['nomClasse']);
        $classe->setAnnee($promotionActuelle->getAnnee());
        $classe->setPromotion($promotionActuelle);
        $manager->persist($classe);
        $manager->flush();
        return $this->redirectToRoute('admin.classes.list');
    }
}