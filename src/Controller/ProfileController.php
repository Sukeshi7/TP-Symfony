<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends BaseController
{
    #[Route('/profile', name: 'profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
		$categories = $this->getCategories($entityManager);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
				'categories' => $categories,
        ]);
    }
}
