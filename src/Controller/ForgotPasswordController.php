<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Uid\Uuid;


class ForgotPasswordController extends BaseController
{
    #[Route('/forgot_password', name: 'forgot_password')]
	public function forgotPassword(Request $request,EntityManagerInterface $entityManager, MailerInterface $mailer): Response
	{
		$categories = $this->getCategories($entityManager);

		if ($request->isMethod('POST')) {
			$email = $request->request->get('email');
			$user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

			if (!$user) {
				$this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
				return $this->redirectToRoute('forgot_password');
			}

			$resetToken = Uuid::v4()->toString();
			$user->setResetToken($resetToken);
			$entityManager->flush();

			$emailMessage = (new TemplatedEmail())
					->from('no-reply@example.com')
					->to($user->getEmail())
					->subject('Réinitialisation de votre mot de passe')
					->htmlTemplate('emails/reset.html.twig')
					->context([
							'resetToken' => $resetToken,
							'user_email' => $user->getEmail(),
					]);

			$mailer->send($emailMessage);

			$this->addFlash('success', 'Un email a été envoyé pour réinitialiser votre mot de passe.');

			return $this->redirectToRoute('app_login');
		}
		return $this->render('forgot_password/index.html.twig', [
				'controller_name' => 'ForgotPasswordController',
				'categories' => $categories,
		]);
	}
}
