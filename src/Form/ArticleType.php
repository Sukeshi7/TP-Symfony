<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
				->add('title', TextType::class, [
						'label' => 'Titre de l\'article',
						'attr' => [
								'placeholder' => 'Entrez le titre ici...',
								'class' => 'w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
						],
				])
				->add('content', TextareaType::class, [
						'label' => 'Contenu de l\'article',
						'attr' => [
								'placeholder' => 'Écrivez votre article ici...',
								'class' => 'w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
								'rows' => 6,
						],
				])
				->add('id_category', EntityType::class, [
						'class' => Category::class,
						'choice_label' => 'name',
						'label' => 'Catégorie',
						'placeholder' => 'Sélectionnez une catégorie',
						'attr' => [
								'class' => 'w-full p-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500',
						],
				])
				->add('id_language', EntityType::class, [
						'class' => Language::class,
						'choice_label' => 'name',
						'label' => 'Langue',
						'placeholder' => 'Sélectionnez une langue',
						'attr' => [
								'class' => 'w-full p-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500',
						],
				])
				->add('id_user', EntityType::class, [
						'class' => User::class,
						'choice_label' => 'email',
						'label' => 'Auteur',
						'placeholder' => 'Sélectionnez un utilisateur',
						'attr' => [
								'class' => 'w-full p-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500',
						],
				]);
	}
	
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
				'data_class' => Article::class,
		]);
	}
}
