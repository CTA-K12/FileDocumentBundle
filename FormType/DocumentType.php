<?php
namespace Mesd\FileDocumentBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Mesd\FileDocumentBundle\Entity\Document;

/**
 * Document Type
 *
 * The service is automatically added when bundle is included at app kernel.
 * You'll have to get the service to use this type in your controller.
 *
 * Usage:
 *     $document = $this->get('document');
 *     $form     = $this->createForm(DocumentType::class, $document);
 *
 *      return $this->render('MesdFileDocumentBundle:Document:upload.html.twig', [
 *          'form' => $form->createView()
 *     ]);
 */
class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('file', FileType::class)
            ->add('category', ChoiceType::class, [
                'choices' => Document::getDirs(),
            ])
            ->add('filename', TextType::class,[
                'required' => false,
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getName()
    {
        return 'mesd_file_documenttype';
    }
}
