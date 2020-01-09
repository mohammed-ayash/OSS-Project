<?php

    namespace App\Controller;

    use App\Entity\Favorite;
    use App\Entity\Product;
    use App\Form\FavoriteType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

    /**
     * @Route("/favorite")
     */
    class FavoriteController extends AbstractController
    {
        /**
         * @Route("/", name="favorite_index", methods={"GET"})
         */
        public function index(): Response
        {
            $favorites = $this->getDoctrine()
                ->getRepository(Favorite::class)
                ->findAll();

            return $this->render('favorite/index.html.twig', [
                'favorites' => $favorites,
            ]);
        }

        /**
         * @Route("/new/{id}", name="favorite_new", methods={"GET","POST"})
         * @param Request $request
         * @param Product $product
         * @param TokenStorageInterface $tokenStorage
         * @return Response
         */
        public function new(Request $request, Product $product, TokenStorageInterface $tokenStorage): Response
        {
            $favorite = new Favorite();

            $token = $tokenStorage->getToken();
            $entityManager = $this->getDoctrine()->getManager();
            $favorite->setUser($token->getUser());
            $favorite->setProduct($product);
            $entityManager->persist($favorite);
            $entityManager->flush();

            return $this->redirectToRoute('favorite_index');


        }

        /**
         * @Route("/{id}", name="favorite_show", methods={"GET"})
         * @param Favorite $favorite
         * @return Response
         * @author Yamen Abd Alrahman
         * @email Yamen@gmail.com
         */
        public function show(Favorite $favorite): Response
        {

            return $this->render('favorite/show.html.twig', [
                'favorite' => $favorite,
            ]);
        }

        /**
         * @Route("/{id}/edit", name="favorite_edit", methods={"GET","POST"})
         * @param Request $request
         * @param Favorite $favorite
         * @return Response
         * @author Yamen Abd Alrahman
         * @email Yamen@gmail.com
         */
        public function edit(Request $request, Favorite $favorite): Response
        {
            $form = $this->createForm(FavoriteType::class, $favorite);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('favorite_index');
            }

            return $this->render('favorite/edit.html.twig', [
                'favorite' => $favorite,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="favorite_delete", methods={"DELETE"})
         * @param Request $request
         * @param Favorite $favorite
         * @return Response
         * @author Mohammad Ayash
         * @email Mohammad@gmail.com
         *
         */
        public function delete(Request $request, Favorite $favorite): Response
        {
            if ($this->isCsrfTokenValid('delete' . $favorite->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($favorite);
                $entityManager->flush();
            }

            return $this->redirectToRoute('favorite_index');
        }
    }
