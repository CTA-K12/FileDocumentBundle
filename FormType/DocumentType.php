<?php

namespace Mesd\FileDocumentBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


// Note:  you will have to 'get the service' to use this type in your controller

// $document = $this->get( 'document' );
// $form   = $this->createForm( new DocumentType(), $document );

// return $this->render(
//     'MesdFileDocumentBundle:Document:upload.html.twig',
//     array(
//         'form' => $form->createView()
//     )
// );
//
// The service is automatically added when bundle is included at app kernel


class DocumentType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ) {

        $builder->add( 'file', 'file' );

        $builder->add( 'category' ,
            'choice' ,
            array( 'choices' => \Mesd\FileDocumentBundle\Entity\Document::getDirs() )
        );

        $builder->add( 'filename', null, array( 'required' => false ) );
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver ) {
        $resolver->setDefaults(
            array(
            )
        );
    }

    public function getName() {
        return 'mesd_file_documenttype';
    }
}
