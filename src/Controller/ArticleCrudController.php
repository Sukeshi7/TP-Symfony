<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Form\ArticleAIConfirmType;
use App\Form\ArticleAIType;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\CohereService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
final class ArticleCrudController extends BaseController
{
    #[Route(name: '/', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
		$categories = $this->getCategories($entityManager);
		
        return $this->render('article_crud/index.html.twig', [
            'articles' => $articleRepository->findAll(),
			'categories' => $categories,
        ]);
    }

    #[Route('/new/manual', name: 'app_article_crud_new_manual', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
		$categories = $this->getCategories($entityManager);
		
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article_crud/new.html.twig', [
            'article' => $article,
            'form' => $form,
			'categories' => $categories,
        ]);
    }
	
	#[Route('/new/ai', name: 'app_article_crud_new_ai', methods: ['GET', 'POST'])]
	public function newAI(Request $request, EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);
		
		$form = $this->createForm(ArticleAIType::class);
		$form->handleRequest($request);
		
		$generatedArticle = null;
		
		if ($form->isSubmitted() && $form->isValid()) {
			$article = $form->getData();
			
			$articleLanguage = $entityManager->getRepository(Language::class)->find($article['language']);
			$articleCategory = $entityManager->getRepository(Category::class)->find($article['category']);
			
			$articleLanguageName = $articleLanguage->getName();
			$articleCategoryName = $articleCategory->getName();
			
			$generatedArticle = $this->generateArticle(
					$article['keywords'],
					$article['description'],
					$articleLanguageName,
					$articleCategoryName,
			);
			
			$session = $request->getSession();
			$session->set('generatedArticle', $generatedArticle);

			return $this->redirectToRoute('app_article_crud_new_ai_confirm');
		}
		
		return $this->render('article_crud/new_ai.html.twig', [
				'categories' => $categories,
				'form' => $form->createView(),
				'generatedArticle' => $generatedArticle,
		]);
	}
	
	private CohereService $cohereService;
	
	public function __construct(CohereService $cohereService)
	{
		$this->cohereService = $cohereService;
	}
	
	private function generateArticle(string $keywords, string $description, string $language, string $category): array
	{
		$prompt = "Contexte: Tu es un journaliste pour un site d'actualités sous forme de blog.
		Génère un titre accrocheur et un article de 200 à 300 mots en $language le thème et les mots-clés suivants : '$keywords'.
		Pour t'aiguiller dans la rédaction de ton article tu peux t'appuyer sur la requête suivante : $description.
		Réponds sous ce format :
		Titre: [Titre de l'article]
		Article: [Contenu de l'article]";
		$generatedArticle = $this->cohereService->generateText($prompt);
		$generatedArticle['language'] = $language;
		$generatedArticle['category'] = $category;
		return $generatedArticle ?: ['Erreur' => 'Erreur lors de la génération de l\'article.'];
	}
	
	#[Route('/new/ai/confirm', name: 'app_article_crud_new_ai_confirm', methods: ['GET', 'POST'])]
	public function newAIConfirm(Request $request, EntityManagerInterface $entityManager): Response
	{
		$categories = $this->getCategories($entityManager);
		$session = $request->getSession();
		
		$generatedArticle = $session->get('generatedArticle', []);
		
		if (!$generatedArticle) {
			$this->addFlash('error', 'Aucun article généré trouvé.');
			return $this->redirectToRoute('app_article_crud_new_ai');
		}
		
		dump($generatedArticle);
		
		$articleLanguage = $entityManager->getRepository(Language::class)->findOneBy(['name' => $generatedArticle['language']]);
		$articleCategory = $entityManager->getRepository(Category::class)->findOneBy(['name' => $generatedArticle['category']]);
		
		$generatedArticle['language'] = $articleLanguage;
		$generatedArticle['category'] = $articleCategory;
		
		$formConfirm = $this->createForm(ArticleAIConfirmType::class, null, [
				'generatedArticle' => $generatedArticle,
		]);
		$formConfirm->handleRequest($request);
		
		if ($formConfirm->isSubmitted() && $formConfirm->isValid()) {
			dump($formConfirm->getData());
			$articleLanguage = $entityManager->getRepository(Language::class)->find($formConfirm['language']->getData());
			$articleCategory = $entityManager->getRepository(Category::class)->find($formConfirm['category']->getData());
			
			$article = new Article();
			$article->setIdCategory($articleCategory);
			$article->setIdLanguage($articleLanguage);
			$article->setTitle($formConfirm['title']->getData());
			$article->setContent($formConfirm['article']->getData());
			$article->setCreationDate(new \DateTime());
			$article->setUpdateDate(new \DateTime());
			$article->setIdUser($this->getUser());
			
			dump($article);
			
			$entityManager->persist($article);
			$entityManager->flush();
			
			return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('article_crud/new_ai_confirm.html.twig', [
				'categories' => $categories,
				'formConfirm' => $formConfirm->createView(),
		]);
	}
	
	#[Route('/{id}', name: 'app_article_crud_show', methods: ['GET'])]
    public function show(Article $article, EntityManagerInterface $entityManager): Response
    {
		$categories = $this->getCategories($entityManager);
		
        return $this->render('article_crud/show.html.twig', [
            'article' => $article,
			'categories' => $categories,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
		$categories = $this->getCategories($entityManager);
		
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			$action = $request->request->get('action');
			
			if ($action === 'update') {
				$article->setUpdateDate(new \DateTime());
			} else if ($action === 'rewrite') {
				$articleLanguage = $entityManager->getRepository(Language::class)->find($article->getIdLanguage());
				$articleCategory = $entityManager->getRepository(Category::class)->find($article->getIdCategory());
				
				$generatedArticle = $this->rewriteArticle(
						$article->getTitle(),
						$article->getContent(),
						$articleLanguage->getName(),
						$articleCategory->getName(),
				);
				
				$article->setTitle($generatedArticle['title']);
				$article->setContent($generatedArticle['article']);
				$article->setUpdateDate(new \DateTime());
			} else if ($action === 'partially_rewrite') {
				$articleLanguage = $entityManager->getRepository(Language::class)->find($article->getIdLanguage());
				$articleCategory = $entityManager->getRepository(Category::class)->find($article->getIdCategory());
				
				$generatedArticle = $this->partiallyRewriteArticle(
						$article->getTitle(),
						$article->getContent(),
						$articleLanguage->getName(),
						$articleCategory->getName(),
				);
				
				$article->setTitle($generatedArticle['title']);
				$article->setContent($generatedArticle['article']);
				$article->setUpdateDate(new \DateTime());
			}
			
			$entityManager->flush();
			return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article_crud/edit.html.twig', [
            'article' => $article,
            'form' => $form,
			'categories' => $categories,
        ]);
    }
	
	private function rewriteArticle(string $title, string $content, string $language, string $category): array
	{
		$prompt = "Contexte: Tu es un journaliste pour un site d'actualités sous forme de blog.
		Voici un article que tu as rédigé. Tu souhaites le retravailler pour le rendre plus attractif.
		Voici l'article à retravailler :
		Titre: [$title]
		Article: [$content]
		Re-génère un article de 200 à 300 mots en $language pour améliorer ton article initial.
		Réponds sous ce format :
		Titre: [Titre de l'article]
		Article: [Contenu de l'article]";
		$generatedArticle = $this->cohereService->generateText($prompt);
		$generatedArticle['language'] = $language;
		$generatedArticle['category'] = $category;
		return $generatedArticle ?: ['Erreur' => 'Erreur lors de la génération de l\'article.'];
	}
	
	private function partiallyRewriteArticle(string $title, string $content, string $language, string $category): array
	{
		$prompt = "Contexte: Tu es un journaliste pour un site d'actualités sous forme de blog.
		Voici un article que tu as rédigé. Tu souhaites retravailler certaines parties pour le rendre plus attractif.
		Tu as balisé les parties à retravailler de cette façon: [+ paragraphe à modifier +].
		Voici l'article à retravailler :
		Titre: [$title]
		Article: [$content]
		Re-génère uniquement les parties à modifier de ton article en $language pour améliorer ton article initial.
		Réponds sous ce format :
		Titre: [Titre de l'article]
		Article: [Contenu de l'article]";
		$generatedArticle = $this->cohereService->generateText($prompt);
		$generatedArticle['language'] = $language;
		$generatedArticle['category'] = $category;
		return $generatedArticle ?: ['Erreur' => 'Erreur lors de la génération de l\'article.'];
	}

    #[Route('/{id}', name: 'app_article_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('/', [], Response::HTTP_SEE_OTHER);
    }
}
