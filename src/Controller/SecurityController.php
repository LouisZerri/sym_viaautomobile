<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;


    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/inscription", name="security_registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $token = $user->str_random(60);

            $user->setConfirmationCle($token);
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Compte créé avec succès');
            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $last_username = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $last_username
        ]);


    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}


    /**
     * @param Request $request
     * @return Response
     * @Route("/parametre-de-compte", name="edit_account")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $user = $this->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->em->flush();
            $this->addFlash('success', 'Votre compte a été modifié avec succès');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/edit_account.html.twig',[
            'form' => $form->createView()
        ]);
    }

}
