<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ArticleController extends BaseController
{
	#[Route('/article/{id}', name: 'article')]
	public function show(int $id, EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		$article = $entityManager->getRepository(Article::class)->find($id);

		if (!$article) {
			return $this->render('article/not_found.html.twig', [
				'categories' => $categories,
			]);
		}

		return $this->render('article/index.html.twig', [
				'article' => $article,
				'categories' => $categories,
		]);
	}
}
