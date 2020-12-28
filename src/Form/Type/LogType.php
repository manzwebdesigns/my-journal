<?php namespace App\Form\Type;

use App\Entity\Log;
use Symfony\Component\Form\{AbstractType, Extension\Core\Type\SubmitType, FormBuilderInterface, FormEvent, FormEvents};
use DateTime;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate logs.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class LogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

	    $log = $options['data'];

        $builder
	        ->add('log_date', DateTimePickerType::class, [
		        'label' => 'Log Date',
		        'data' => $log->getLogDate(),
	        ])
            ->add('log_message', CKEditorType::class, [
	            'attr' => ['autofocus' => true],
                'label' => 'Log Entry',
            ])
	        ->add('submit', SubmitType::class, [
	        	'attr' => ['class' => 'btn btn-primary'],
	        ])
            // form events let you modify information or fields at different steps
            // of the form handling process.
            // See https://symfony.com/doc/current/form/events.html
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Log */
                $log = $event->getData();
                if (null !== $log->getLogMessage()) {
                    $log->setLogDate($log->getLogDate())
                    ->setUserId($log->getUserId());
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Log::class,
        ]);
    }
}
