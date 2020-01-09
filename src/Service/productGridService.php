<?php

namespace App\Service;

use App\Entity\User;
use App\Lib\DataGrid;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\Writer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

class productGridService extends DataGrid
{

    private $paginator;
    protected $em;
    private $formFactory;

    //Arguments style is compatible with PSR-12
    public function __construct(
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory
    ) {
        $this->paginator = $paginator;
        $this->em = $em;
        $this->formFactory = $formFactory;
    }
    //for get the data from DB
    public function getGridData($page = 1)
    {
        $parameters = [];
        //create the query
        $dql = "SELECT p 
                FROM App:Product p
                WHERE 1=1";

        $searchValue = $this->getFormDataElement('search');
        //check if the use  search
        if($searchValue) {
            $dql .= 'AND MATCH (p.name) AGAINST (:search IN BOOLEAN MODE) > 0 ';
            $parameters['search'] = $searchValue;
        }
        //check if the not use search
        if(empty($searchValue)) {
            $dql .= 'ORDER BY p.id DESC ';
        }
        //get query from DB
        $query = $this->em->createQuery($dql);
        if(count($parameters)) {
            $query->setParameters($parameters);
        }
        //set pagination
        $pagination = $this->paginator->paginate(
            $query,
            $page,
            6,
            ['wrap-queries' => true]
        );

        return $pagination;
    }
    //create method form search
    public function getFilterForm($formActionUrl, $data = null, $options = [])
    {
        $form = $this->formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('search', TextType::class, [
                'label' => 'Search'
            ])
        ;
        return $form->getForm();
    }
}
