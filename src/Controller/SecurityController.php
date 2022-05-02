<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/password", name="password")
     * @return Response
     */
    public function changePassword(): Response
    {
        return $this->render('security/motDePasse.html.twig');
    }

    /**
     * @Route("/passwordSubmit", name="password.submit")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function changePasswordSubmit(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if($request->get('nouveauMdp') != $request->get('confirmerNouveauMdp')){
            return $this->render('security/motDePasse.html.twig', [
                'ancienMdp' => $request->get('ancienMdp'),
                'nouveauMdp' => $request->get('nouveauMdp'),
                'confirmerNouveauMdp' => $request->get('confirmerNouveauMdp'),
                'error' => "Les nouveau mots de passe ne correspondent pas."
            ]);
        }
        $user->setPassword($this->passwordEncoder->encodePassword($user, $request->get('nouveauMdp')));
        $manager->persist($user);
        $manager->flush();
        return $this->render('home.html.twig');
    }
}