<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use DoctrineExtensions\Query\Oracle\ToChar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/country")
 */
class CountryController extends AbstractController
{
    /**
     * @Route("/", name="country_index", methods={"GET"})
     */
    public function index(): Response
    {
        $countries = $this->getDoctrine()
            ->getRepository(Country::class)
            ->findAll();

        return $this->render('country/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    /**
     * @Route("/Setowner", name="country_new", methods={"GET","POST"})
     */
    public function Setowner(Request $request ,TokenStorageInterface $tokenStorage): Response
    {
        $token=$tokenStorage->getToken();
        $user=$token->getUser();
        $status = $user->gettype();
        $user->settype(!$status);
        $this->getDoctrine()->getManager()->flush();

        $newStatus = $user->gettype() ? 'owner shop' : 'Customer';
        $this->get('session')->getFlashBag()->add(
            'success', "The status of the user ({$user->getUsername()}) has been changed to \"{$newStatus}\""
        );

        $referer = $request->headers->get('referer');
        if($referer) {
            return $this->redirect($referer);
        }else{
            return $this->redirectToRoute('user_list');
        }
    }

    /**
     * @Route("/new", name="country_new2", methods={"GET","POST"})
     */
    public function new(Request $request ,TokenStorageInterface $tokenStorage): Response
    {
        $token=$tokenStorage->getToken();
        $user=$token->getUser();
        $status = $user->gettype();
        $user->settype(!$status);
        $this->getDoctrine()->getManager()->flush();

        $newStatus = $user->gettype() ? 'owner shop' : 'Customer';
        $this->get('session')->getFlashBag()->add(
            'success', "The status of the user ({$user->getUsername()}) has been changed to \"{$newStatus}\""
        );

        $referer = $request->headers->get('referer');
        if($referer) {
            return $this->redirect($referer);
        }else{
            return $this->redirectToRoute('user_list');
        }
    }

    /**
     * @Route("/{id}", name="country_show", methods={"GET"})
     */
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="country_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Country $country): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('country_index');
        }

        return $this->render('country/edit.html.twig', [
            'country' => $country,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="country_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Country $country): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($country);
            $entityManager->flush();
        }

        return $this->redirectToRoute('country_index');
    }
}
