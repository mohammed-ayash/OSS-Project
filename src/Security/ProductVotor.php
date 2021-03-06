<?php


namespace App\Security;


use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVotor extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    protected function supports(string $attribute, $subject)
    {

        if (!in_array($attribute,[self::EDIT,self::DELETE])){
            return false;
        }
        //check if the validation in product subject
        if (!$subject instanceof Product){
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $authentictedUser =$token->getUser();

        if (!$authentictedUser instanceof User){
            return false;
        }
        //check if the user is admin
        if ($authentictedUser->getRoles()[0]== 'ROLE_Admin' )
            return true;
        /**
         * @var Product $product
         */
        $product=$subject;

        //check if the  product to this user
        return  $product->getUser()->getid() === $authentictedUser->getid();
    }
}