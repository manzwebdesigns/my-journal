<?php namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, EmailType, PasswordType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{IsTrue, Length, NotBlank};

/**
 * Class RegistrationFormType
 * @package App\Form\Type
 */
class RegistrationFormType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
	            'label' => 'Username',
            ])
            ->add('fullName', TextType::class, [
	            'label' => 'Name',
            ])
            ->add('email', EmailType::class, [
	            'label' => 'Email',
            ])
            ->add('password', PasswordType::class, [
	            // instead of being set onto the object directly,
	            // this is read and encoded in the controller
	            'mapped' => false,
	            'label' => 'Password',
	            'constraints' => [
		            new NotBlank([
			            'message' => 'Please enter a password',
		            ]),
		            new Length([
			            'min' => 6,
			            'minMessage' => 'Your password should be at least {{ limit }} characters',
			            // max length allowed by Symfony for security reasons
			            'max' => 4096,
		            ]),
	            ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
