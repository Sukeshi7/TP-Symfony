<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends BaseController
{
	#[Route('/admin', name: 'admin')]
	public function index(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		return $this->render('admin/index.html.twig', [
				'categories' => $categories,
		]);
	}

	#[Route('/admin/user', name: 'admin_user')]
	public function manageUsers(EntityManagerInterface $entityManager, Request $request): Response
	{
		$categories = $this->getCategories($entityManager);

		$users = $entityManager->getRepository(User::class)->findAll();

		return $this->render('admin/user.html.twig', [
				'users' => $users,
				'categories' => $categories,
		]);
	}

	#[Route('/admin/article', name: 'admin_article')]
	public function manageArticles(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		$articles = $entityManager->getRepository(Article::class)->findAll();

		return $this->render('admin/article.html.twig', [
				'articles' => $articles,
				'categories' => $categories,
		]);
	}

	#[Route('/admin/comment', name: 'admin_comment')]
	public function manageComments(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		$comments = $entityManager->getRepository(Comment::class)->findAll();

		return $this->render('admin/comment.html.twig', [
				'comments' => $comments,
				'categories' => $categories,
		]);
	}

	#[Route('/admin/category', name: 'admin_category')]
	public function manageCategories(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		return $this->render('admin/category.html.twig', [
				'categories' => $categories,

		]);
	}

	#[Route('/admin/language', name: 'admin_language')]
	public function manageLanguages(EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);

		$languages = $entityManager->getRepository(Language::class)->findAll();

		return $this->render('admin/language.html.twig', [
				'languages' => $languages,
				'categories' => $categories,
		]);
	}
}
