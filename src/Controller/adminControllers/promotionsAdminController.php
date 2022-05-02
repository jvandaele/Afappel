<?php

namespace App\Controller\adminControllers;

use App\Entity\Promotion;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class promotionsAdminController extends AbstractController {

    /**
     * @Route("/admin/promotions", name="admin.promotions.list")
     * @param Environment $twig
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showList(Environment $twig,  EntityManagerInterface $manager): Response
    {
        $promotions = $manager->getRepository(Promotion::class)->findAll();
        $promotionActuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
        return new Response($twig->render('Admin/promotionsAdmin/promotionsAdminList.html.twig', ["promotions" => $promotions, "promotionActuelle" => $promotionActuelle]));
    }

    /**
     * @Route("/admin/promotions/show/{id}", name="admin.promotions.show")
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
        $promotion = $manager->getRepository(Promotion::class)->findOneBy(['id' => $id]);
        if($promotion != null){
            return new Response($twig->render('Admin/promotionsAdmin/promotionsAdminShow.html.twig', ["promotion" => $promotion]));
        }
        return new Response($twig->render('404NotFound.html.twig'));
    }

    /**
     * @Route("/admin/promotions/create", name="admin.promotions.create")
     * @param Environment $twig
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Environment $twig): Response
    {
        return new Response($twig->render('Admin/promotionsAdmin/promotionsAdminCreate.html.twig'));
    }

    /**
     * @Route("/admin/promotions/submitCreate", name="admin.promotions.submitCreate")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function createSubmit(Request $request, EntityManagerInterface $manager): RedirectResponse
    {
        $new_promotion = new Promotion();
        $new_promotion->setNomPromotion($request->get('nom'));
        $new_promotion->setAnnee($request->get('annee'));
        if($request->get('periode') == 0){ $new_promotion->setPeriode("S1-S3"); }
        else{ $new_promotion->setPeriode("S2-S4"); }
        if($request->get('actuelle') != null){
            $promotion_actuelle = $manager->getRepository(Promotion::class)->findOneBy(['actuelle' => true]);
            if($promotion_actuelle != null){
                $promotion_actuelle->setActuelle(false);
                $manager->persist($promotion_actuelle);
                $manager->flush();
            }
            $new_promotion->setActuelle(true);
        }
        else{ $new_promotion->setActuelle(false); }
        $manager->persist($new_promotion);
        $manager->flush();
        return $this->redirectToRoute('admin.promotions.list');
    }
}