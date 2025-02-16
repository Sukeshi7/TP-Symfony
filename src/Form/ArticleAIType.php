<?php
	
	namespace App\Form;
	
	use App\Entity\Category;
	use App\Entity\Language;
	use Symfony\Bridge\Doctrine\Form\Type\EntityType;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use Symfony\Component\Form\Extension\Core\Type\TextType;
	use Symfony\Component\Form\Extension\Core\Type\TextareaType;
	
	class ArticleAIType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options): void
		{
			$builder
					->add('category', EntityType::class, [
							'class' => Category::class,
							'choice_label' => 'name',
							'placeholder' => 'Choisir une catégorie',
							'label' => 'Catégorie',
							'required' => true,
							'attr' => ['class' => 'border border-gray-300 p-2 rounded-md w-full'],
					])
					->add('language', EntityType::class, [
							'class' => Language::class,
							'choice_label' => 'name',
							'placeholder' => 'Choisir une langue',
							'label' => 'Langue',
							'required' => true,
							'attr' => ['class' => 'border border-gray-300 p-2 rounded-md w-full'],
					])
					->add('keywords', TextType::class, [
							'label' => 'Mots-clés',
							'required' => true,
							'attr' => ['class' => 'border border-gray-300 p-2 rounded-md w-full'],
					])
					->add('description', TextareaType::class, [
							'label' => 'Description',
							'required' => true,
							'attr' => ['class' => 'border border-gray-300 p-2 rounded-md w-full h-40'],
					]);
		}
		
		public function configureOptions(OptionsResolver $resolver): void
		{
			$resolver->setDefaults([]);
		}
	}
