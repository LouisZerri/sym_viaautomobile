<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\notification\EmailNotification;
use App\Repository\UserRepository;
use App\Services\WeekFormat;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Access;
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
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, EmailNotification $notification)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $token = $user->str_random(60);
            $user->setRoles('ROLE_USER');
            $user->setConfirmationCle($token);
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            $notification->notify($user);

            return $this->redirectToRoute('validation');
        }

        return $this->render('security/registration.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @return Response
     * @Route("/validate-account", name="validation")
     */
    public function validateAccount()
    {
        return $this->render('security/validate.html.twig');
    }

    /**
     * @Route("/confirmation-de-compte/{id}-{token}", name="confirmation")
     * @param int $id
     * @param string $token
     * @return RedirectResponse
     */
    public function confirmationAccount(int $id, string $token)
    {

        $user = $this->userRepository
            ->findOneBy(['id' => $id]);

        if($user && $user->getConfirmationCle() == $token)
        {
            $user->setConfirmationCle(null);
            $user->setConfirmedAt(new \DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Votre compte a bien été validé');
            return $this->redirectToRoute('security_login');
        }
        else
        {
            $this->addFlash('error', 'Ce token n\'est plus valide');
            return $this->redirectToRoute('security_login');
        }
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
     * @Route("/forgotpassword", name="forgot-password")
     * @param Request $request
     * @param EmailNotification $notification
     * @return Response
     */
    public function forgotPassword(Request $request, EmailNotification $notification)
    {
        $errors = [];

        if($request->getMethod() == 'POST')
        {
            $data = $request->request->all();

            if(empty($data) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            {
                $errors['email'] = 'Votre email n\'est pas valide';
            }

            $user = $this->userRepository
                ->findOneBy(['email' => $data['email']]);

            if($user == null)
            {
                $errors['emailexist'] = 'Cet email n\'existe pas';
            }

            if(empty($errors))
            {
                $token = $user->str_random(60);
                $user->setResetToken($token);
                $user->setResetAt(new \DateTime());
                $this->em->flush();
                $notification->sendEmailForResetPassword($user);
                $this->addFlash('success', 'Un email vous a été envoyé afin de créer un nouveau mot de passe');
            }

        }

        return $this->render("security/forgotpassword.html.twig", [
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/reset-password/{id}-{token}", name="reset")
     * @param int $id
     * @param string $token
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function generateNewPassword(int $id, string $token, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $errors = [];

        if(isset($id) && isset($token))
        {
            $user = $this->userRepository
                ->getUserForResetPassword($id, $token);

            if($user)
            {
                if($request->getMethod() == 'POST')
                {
                    $data = $request->request->all();

                    if(!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{9,}$#', $data['password']))
                    {
                        $errors['match'] = 'Le mot de passe n\'est pas assez securisé';
                    }

                    if($data['password'] != $data['confirm_password'])
                    {
                        $errors['password'] = 'Les mots de passes ne correspondent pas';
                    }

                    if(empty($errors))
                    {
                        $hash = $encoder->encodePassword($user, $data['password']);
                        $user->setPassword($hash);
                        $this->em->flush();
                        $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                        return $this->redirectToRoute('security_login');

                    }
                }

                return $this->render('security/reset.html.twig', [
                    'errors' => $errors
                ]);

            }
            else
            {
                $this->addFlash('error', 'Ce token n\'est plus valide');
                return $this->redirectToRoute('security_login');
            }



        }
        else
        {
            $this->redirectToRoute('login');
        }


    }


    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}


    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param WeekFormat $weekFormat
     * @return Response
     * @Access("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
     * @Route("/parametre-de-compte", name="edit_account")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $encoder, WeekFormat $weekFormat)
    {
        $user = $this->getUser();

        $retour = $weekFormat->weekToString(date('Y'), (date('W') - 1));

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
            'form' => $form->createView(),
            'semaine' => $retour
        ]);
    }

}
