<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CategoriesController extends BaseController
{
	/*#[Route('/categories', name: 'categories')]
	public function list(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		return $this->render('categories/index.html.twig', [
				'categories' => $categories,
				'controller_name' => 'CategoriesController',
		]);
	}*/

	#[Route('/categories/{id}', name: 'category_articles')]
	public function show(int $id, EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		$category = $entityManager->getRepository(Category::class)->find($id);
		$articles = $entityManager->getRepository(Article::class)->findBy(['id_category' => $id]);

		if (!$category) {
			return $this->render('categories/not_found.html.twig', [
					'categories' => $categories,
			]);
		}

		return $this->render('categories/articles.html.twig', [
				'category' => $category,
				'articles' => $articles,
				'categories' => $categories,
		]);
	}

}
