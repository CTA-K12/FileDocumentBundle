<?php

namespace Mesd\File\DocumentBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ) {

        $builder->add( 'file', 'file' );
        $builder->add( 'category'
            , 'choice'
            , array('choices' => \Mesd\File\DocumentBundle\Entity\Document::getDirs()) );
        $builder->add( 'filename', null, array('required' => false) );
    }

    public function setDefaultOptions( OptionsResolverInterface $resolver ) {
        $resolver->setDefaults( array(
            ) );
    }

    public function getName() {
        return 'mesd_file_documenttype';
    }
}
