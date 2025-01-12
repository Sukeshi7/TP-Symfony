<?php
namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
	#[Route('/', name: 'app_homepage')]
	public function index(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		// Récupérer les 4 articles les plus récents
		$recentArticles = $entityManager->getRepository(Article::class)
				->findBy([], ['creation_date' => 'DESC'], 4);

		// Approche avec offset aléatoire pour récupérer 4 articles
		$articleRepository = $entityManager->getRepository(Article::class);
		$totalArticles = $articleRepository->count([]);
		$offset = max(0, rand(0, $totalArticles - 4));
		$randomArticles = $articleRepository->findBy([], null, 4, $offset);

		return $this->render('home/index.html.twig', [
				'controller_name' => 'HomeController',
				'categories' => $categories,
				'recentArticles' => $recentArticles,
				'randomArticles' => $randomArticles,
		]);
	}
}
