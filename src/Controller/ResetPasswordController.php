<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends BaseController
{
	#[Route('/reset_password/{token}', name: 'reset_password')]
	public function resetPassword(string $token, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
	{
		$categories = $this->getCategories($entityManager);

		$user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

		if (!$user) {
			$this->addFlash('error', 'Le token de réinitialisation est invalide ou expiré.');
			return $this->redirectToRoute('forgot_password');
		}

		if ($request->isMethod('POST')) {
			$password = $request->get('password');
			$passwordConfirm = $request->get('password_confirm');

			if ($password !== $passwordConfirm) {
				$this->addFlash('error', 'Les mots de passe ne correspondent pas.');
			} else {
				$hashedPassword = $passwordHasher->hashPassword($user, $password);
				$user->setPassword($hashedPassword);
				$user->setResetToken(null); // Supprime le token après utilisation
				$entityManager->flush();

				$this->addFlash('success', 'Votre mot de passe a été mis à jour.');
				return $this->redirectToRoute('app_login');
			}
		}

		return $this->render('reset_password/index.html.twig', [
				'token' => $token,
				'controller_name' => 'ResetPasswordController',
				'categories' => $categories,
		]);
	}

}
