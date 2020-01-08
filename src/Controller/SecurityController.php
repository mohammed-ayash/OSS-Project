<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils ,TokenStorageInterface $tokenStorage)
    {
        $token=$tokenStorage->getToken();
        if ($token->getUser() != "anon."){
            return $this->redirectToRoute('product_index');
        }
        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * @Route("/register", name="register_index", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder,TokenStorageInterface $tokenStorage): Response
    {
        $token=$tokenStorage->getToken();
        if ($token->getUser() != "anon."){
            return $this->redirectToRoute('product_index');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user,$form ->get('password')->getData()));
            $entityManager->persist($user);
            $entityManager->flush();

            $uploadedFile =$form ->get('picture')->getData();
            $entityManager->getRepository(User::class)->uploadPicture($user, $uploadedFile);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }

}
