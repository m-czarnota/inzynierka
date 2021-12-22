<?php

namespace App\Controller\Register;

use App\Entity\User;
use App\Entity\Score;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UserRepository $userRepository;

    public function __construct(EmailVerifier $emailVerifier, UserRepository $userRepository)
    {
        $this->emailVerifier = $emailVerifier;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $score = new Score();
            $score->setUser($user);
            $user->setScore($score);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_register_email_confirm', ['id' => base64_encode($user->getId())]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @Route (path="email_confirm", name="app_register_email_confirm")
     */
    public function sendConfirmEmail(Request $request): Response
    {
        $id = intval(base64_decode($request->get('id')));
        $user = $this->userRepository->find($id);

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('email_sender'), 'Mail Bot'))
//                    ->to($user->getEmail())
                ->to($this->getParameter('email_recipient'))
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'user' => $user,
                ])
        );

        return $this->render('registration/confirmation_email_sent.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        try {
            $id = intval(base64_decode($request->get('id')));
            $user = $this->userRepository->find($id);
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->render('registration/register_confirm.html.twig', [
                'message' => 'Error occurred when account has been verified.',
            ]);
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
