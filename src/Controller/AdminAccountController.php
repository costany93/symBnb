<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * permet à un administrateur de se connecter 
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastname = $utils->getLastUsername();
        return $this->render('admin/account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $lastname
        ]);
    }

    /**
     * permet a un administrateur de se déconnecter
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout(){

    }
}
