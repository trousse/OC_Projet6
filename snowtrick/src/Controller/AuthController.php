<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\PasswordResetType;
use App\Form\UserSubformType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * @Route("/auth", name="auth_")
 */
class AuthController extends AbstractController
{
    /**
     * @Route("/login",name = "login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/signup", name="register")
     */
    public function signup(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserSubformType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->getData()->getPassword()
                );
                $user->setPassword($hashedPassword);
                $userRepository->add($user);
                $this->addFlash(
                    'success',
                    "votre compte a bien été crée"
                );
            } catch (\Exception $e) {
                $this->addFlash(
                    'danger',
                    "Nom ou mail deja utilisé"
                );
            }
            //  return $this->redirectToRoute('auth_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/sub_user.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    public function forgetPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(ForgotPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(array('email' => $user->getEmail()));
            if ($user) {
                $user->setToken(md5(random_bytes(10)));
                $userRepository->add($user);
                $message = (new TemplatedEmail())
                    ->subject('SnowTricks - Réinitilisation du mot de passe')
                    ->from('noreply@snowtricks.com')
                    ->to("ths.rousse@gmail.com")
                    ->htmlTemplate('emails/reset.html.twig')
                    ->context(['user' => $user]);
                try {
                    $mailer->send($message);
                } catch (TransportExceptionInterface $transportException) {
                    echo $transportException->getMessage();
                }

                $this->addFlash(
                    'success',
                    "un email a été envoyer sur votre boite mail"
                );
            }
        }
        return $this->renderForm('security/forgot_password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/reset-password", name="password_reset")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $username
     * @param $token
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        if ($request->query->has('username'))
            $username = $request->query->get('username');
        if ($request->query->has('token'))
            $token = $request->query->get('token');

        $user = $userRepository->findOneBy(array('username' => $username));
        $form = $this->createForm(PasswordResetType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getToken() === $token) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->getData()->getPassword()
                );
                $user->setPassword($hashedPassword);
                $userRepository->add($user);

                $this->addFlash(
                    'success',
                    "Mot de passe modifié avec succès !"
                );

            } else {
                $this->addFlash(
                    'danger',
                    "La modification du mot de passe a échoué ! Le lien de validation a expiré !"
                );
            }
        }
        else {
            $this->addFlash(
                'danger',
                "La modification du mot de passe a échoué !"
            );
        }
        return $this->renderForm('security/password_form.twig.html', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/valid", name="valid")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $username
     * @param $token
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function validUser(Request $request, UserRepository $userRepository, $username, $token, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $userRepository->findOneBy(array('username' => $username));
        $form = $this->createForm(PasswordResetType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getToken() === $token) {
                $user->setEnabled(true);
                $userRepository->add($user);

                $this->addFlash(
                    'success',
                    "compte activé!"
                );
            } else {
                $this->addFlash(
                    'danger',
                    " Le lien est invalid !"
                );
            }
        }

        return $this->redirectToRoute('auth_login', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
