<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserConfirmationEmailNotReceived;
use App\Event\UserRegistered;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticationAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EmailVerifier $emailVerifier
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticationAuthenticator $authenticator, UserAuthenticatorInterface $userAuthenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->eventDispatcher->dispatch(new UserRegistered($user));

            $this->addFlash('success', 'Votre compte a bien été créé, vous pouvez vous connecter.');

            // generate a signed url and email it to the user
            //            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //                (new TemplatedEmail())
            //                    ->to($user->getEmail())
            //                    ->subject('Merci de confirmer votre email')
            //                    ->htmlTemplate('registration/confirmation_email.html.twig')
            //            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            $this->addFlash('success', 'Votre email a bien été vérifié.');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/validate/email', name: 'app_validate_email')]
    public function validateUserMail(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }
        if ($user->isVerified()) {
            return $this->redirectToRoute('app_home');
        }
        $acceptForm = $this->createFormBuilder()
            ->add('accept', SubmitType::class, ['label' => 'Envoyer un mail de confirmation'])
            ->getForm();
        $acceptForm->handleRequest($request);

        if ($acceptForm->isSubmitted() && $acceptForm->isValid()) {
            /* @var SubmitButton $submitButton */

            $submitButton = $acceptForm->get('accept');

            if ($submitButton->isClicked()) {
                $this->eventDispatcher->dispatch(new UserConfirmationEmailNotReceived($user));

                $this->addFlash('success', 'Un mail de confirmation vous a été envoyé.');

                return $this->redirectToRoute('app_validate_email');
            }
        }

        return $this->render('registration/validate_email.html.twig', [
            'user' => $user,
            'accept_form' => $acceptForm->createView(),
        ]);
    }
}
