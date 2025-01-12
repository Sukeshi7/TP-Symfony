<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class BaseController extends AbstractController
{
	public function getCategories(EntityManagerInterface $entityManager): array
	{
		return $entityManager->getRepository(Category::class)->findAll();
	}
}
