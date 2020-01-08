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
        /**
         * @var Favorite $favorite
         */
        $favorite=$subject;

        return  $favorite->getUser()->getid() === $authentictedUser->getid();
    }
}