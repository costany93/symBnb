<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AccountController extends AbstractController
{
    public function __construct(EntityManagerInterface $em,UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }
    /**
     * permet de se connecter
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastname = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'lastname' => $lastname
        ]);
    }

    /**
     * permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        
    }

    /**
     * permet de s'enregistrer dans le site
     * @Route("/account/register", name="account_register")
     */
    public function register(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $password = $this->encoder->encodePassword($user, $user->getHash());

            $user->setHash($password);
            $this->em->persist($user);

            $this->em->flush();

            $this->addFlash(
                'success',
                'votre compte a bien été créé connectez-vous'
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet d'afficher le formulaire de modification de profil
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profil(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            $this->addFlash(
                'success',
                'Les données ont été modifié avec succès'
            );
        }
        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }
    /**
     * Permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request):Response
    {
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //nous utulisons ici la fonction de vérification de mot de passe de symfony qui nous permet de comparer deux mot de passe, il prend en 1er parametre le mot de passe en clair et en 2 ème le mot de passe hasher avec lequel on veut le comparer
            //Ici nous comparons l'ancien mot de passe tapez à l'ancien mot de passe qui se trouve dans la base de données
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                //ici nous accédons au champs oldPassword afin d'y mettre une erreur personnaliser en cas d'erreur 
                $form->get('oldPassword')->addError(new FormError('Ancien mot de passe incorrect'));
            }
            else
            {
                //on récupère le nouveau mot de passe entrer par utilisateur
                $newPassword = $passwordUpdate->getNewPassword();

                //on le hash pour qu'il soit encoder
                $hash = $this->encoder->encodePassword($user,$newPassword);

                //on remplace le mot de passe qui se trouve dans la base de données par le nouveau et on persist 
                $user->setHash($hash);
                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash('success','Votre mot de passe a bien été modifier');

                return $this->redirectToRoute('home_index');
            }
        }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * permet d'afficher le profil de l'utilisateur connectez
     * 
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount(): Response
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig',[
            'user' => $user
        ]);
    }

    /**
     * Permet d'afficher la liste des réservations fais par un utilisateur
     * @Route("/account/bookings", name="account_bookings")
     * @return Response
     */
    public function booking(){
        return $this->render('account/bookings.html.twig');
    }
}
