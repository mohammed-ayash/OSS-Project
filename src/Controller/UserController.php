<?php

    namespace App\Controller;

    use App\Entity\User;
    use App\Form\UserType;
    use Knp\Component\Pager\PaginatorInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    /**
     * @Route("/user")
     */
    class UserController extends AbstractController
    {
        /**
         * @Route("/", name="user_index", methods={"GET"})
         * @param PaginatorInterface $paginator
         * @param Request $request
         * @return Response
         * @author Yamen Abd Alrahman
         */
        public function index(Request $request, PaginatorInterface $paginator): Response
        {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery("SELECT u from App:User u");

            $users = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10,
                ['wrap-queries' => true]

            );

            return $this->render('user/index.html.twig', [
                'users' => $users,
            ]);
        }

        /**
         * @Route("/new", name="user_new", methods={"GET","POST"})
         * @param UserPasswordEncoderInterface $passwordEncoder Encode Password To unread text
         * @param Request $request
         * @return Response
         * @author Yamen Abd Alrahman
         */
        public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
        {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
                $entityManager->persist($user);
                $entityManager->flush();

                $uploadedFile = $form->get('picture')->getData();
                $entityManager->getRepository(User::class)->uploadPicture($user, $uploadedFile);

                return $this->redirectToRoute('user_index');
            }

            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="user_show", methods={"GET"})
         * @param User $user The User Which I Want Show
         * @return Response
         * @author Yamen Abd Alrahman
         * @email Yammen@gmail.com
         */
        public function show(User $user): Response
        {
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }

        /**
         * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
         * @param Request $request
         * @param User $user
         * @return Response
         * @author Abdulrahman Alhaja
         * @email Abd@gmail.com
         */
        public function edit(Request $request, User $user): Response
        {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_index');
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="user_delete", methods={"DELETE"})
         * @param Request $request
         * @param User $user
         * @return Response
         * @author Abdultahman alhaja
         * @email Abd@gmail.com
         */
        public function delete(Request $request, User $user): Response
        {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('user_index');
        }
    }
