<?php

    namespace App\Controller;

    use App\Entity\Product;
    use App\Entity\Video;
    use App\Form\ProductType;
    use App\Service\productGridService;
    use App\Service\Search;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


    /**
     * @Route("/product")
     */
    class ProductController extends AbstractController
    {
        /**
         * @var AuthorizationCheckerInterface
         */
        private $authorizationChecker;


        public function __construct(AuthorizationCheckerInterface $authorizationChecker)
        {
            $this->authorizationChecker = $this->authorizationChecker;
        }



        /**
         * @Route("/", name="product_index", methods={"GET","POST"})
         * @param Request $request To get all parameter
         * @param productGridService $productService Find all product by name and send data to view
         * @return Response
         * @author Nohammad Ayahs
         */
        public function index(Request $request, productGridService $productService): Response
        {
            $form = $productService->getFilterForm($this->generateUrl('product_index'));
            if ('POST' == $request->getMethod()) {
                $form->handleRequest($request);
                return $this->redirectToRoute('product_index', [
                    'filter' => $productService->getEncodedFilterArray($form)
                ]);
            }
            $filter = $request->query->get('filter');
            if ($filter) {
                $formData = $productService->decodeFilterArray($filter);
                $form = $productService->setFormFilterData($form, $formData);
            }
            $products = $productService->getGridData($request->query->getInt('page', 1));

            return $this->render('product/index.html.twig', [
                'products' => $products,
                'formFilter' => $form->createView(),
            ]);
        }


        /**
         * @Route("/search", name="product_search", methods={"GET","POST"})
         * @param Request $request To get all parameter
         * @param productGridService $productService Find all product by name and send data to view
         * @return Response
         * @author Nohammad Ayahs
         */
        public function search(Request $request, productGridService $productService): Response
        {
            $form = $productService->getFilterForm($this->generateUrl('product_search'));
            if ('POST' == $request->getMethod()) {
                $form->handleRequest($request);
                return $this->redirectToRoute('product_search', [
                    'filter' => $productService->getEncodedFilterArray($form)
                ]);
            }
            $filter = $request->query->get('filter');
            if ($filter) {
                $formData = $productService->decodeFilterArray($filter);
                $form = $productService->setFormFilterData($form, $formData);
            }
            $products = $productService->getGridData($request->query->getInt('page', 1));

            return $this->render('product/search.html.twig', [
                'products' => $products,
                'formFilter' => $form->createView(),
            ]);

        }

        /**
         * @Route("/new", name="product_new", methods={"GET","POST"})
         * @param Request $request To get all parameter
         * @param TokenStorageInterface $tokenStorage Token for get user information in this page
         * @return Response
         * @author Nohammad Ayahs

         */
        public function new(Request $request, TokenStorageInterface $tokenStorage): Response
        {
            $product = new Product();
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $token = $tokenStorage->getToken();
                $entityManager = $this->getDoctrine()->getManager();
                $product->setUser($token->getUser());
                $entityManager->persist($product);
                $entityManager->flush();

                $uploadedFile = $form->get('picture')->getData();
                $entityManager->getRepository(Product::class)->uploadPicture($product, $uploadedFile);

                return $this->redirectToRoute('product_index');
            }

            return $this->render('product/new.html.twig', [
                'product' => $product,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="product_show", methods={"GET"})
         * @param Product $product The product which i want to view
         * @return Response
         * @author Yamen Abd Alrahman
         * @email Yammen@gmail.com
         */
        public function show(Product $product): Response
        {
            return $this->render('product/show.html.twig', [
                'product' => $product,
            ]);
        }

        /**
         * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
         * @Security("is_granted('edit', product)", statusCode=404, message="Resource not found.")
         * @param Request $request
         * @param Product $product Product Which I Want To Edit It
         * @return Response
         * @author Yamen Abd Alrahman
         * @email Yammen@gmail.com

         */
        public function edit(Request $request, Product $product): Response
        {
            //  if(!$this->authorizationChecker->isGranted('edit',$product)){
            //    return $this->redirectToRoute('security_login');
            // }
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('product_index');
            }

            return $this->render('product/edit.html.twig', [
                'product' => $product,
                'form' => $form->createView(),
            ]);
        }

        /**
         * @Route("/{id}", name="product_delete", methods={"DELETE"})
         * @Security("is_granted('delete', product)", statusCode=404, message="Resource not found.")
         * @param Request $request Product Which I Want To Delete It
         * @param Product $product
         * @return Response
         * @author Abdulrahman Alhajja
         * @email Abd@gmail.com
         */
        public function delete(Request $request, Product $product): Response
        {
            //  if(!$this->authorizationChecker->isGranted('edit',$product)){
            //     return $this->redirectToRoute('security_login');
            // }

            if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($product);
                $entityManager->flush();
            }

            return $this->redirectToRoute('product_index');
        }
    }
