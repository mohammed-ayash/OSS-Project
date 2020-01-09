<?php


namespace App\Security;


use App\Entity\Favorite;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FavoriteVotor extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute,[self::EDIT,self::DELETE])){
            return false;
        }
        //check if the validation in Favorite subject
        if (!$subject instanceof Favorite){
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
         * @var Favorite $favorite
         */
        $favorite=$subject;
        //check if the favorite product to this user
        return  $favorite->getUser()->getid() === $authentictedUser->getid();
    }
}