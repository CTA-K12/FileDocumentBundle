<?php

namespace MESD\File\DocumentBundle\Controller;
use MESD\File\DocumentBundle\Entity\Document;
use MESD\File\DocumentBundle\FormType\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentController extends Controller
{
    public function uploadAction( Request $request ) {
        $document = $this->get( 'document' );

        $form = $this->createForm( new DocumentType()
            , $document
        );

        return $this->render( 'MESDFileDocumentBundle:Document:upload.html.twig'
            , array(
                'form' => $form->createView()
            ) );
    }

    public function receiveUploadAction( Request $request ) {
        $document = $this->get( 'document' );

        $form = $this->createForm( new DocumentType()
            , $document
        );

        $form->bindRequest( $request );
        // var_dump($form);die;

        if ( $form->isValid() ) {
            if ( $document->getFilename() === null ) {
                $document->setFilename( $document->getFile()->getClientOriginalName() );
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist( $document );
            $em->flush();

            return $this->redirect( $this->generateUrl( 'default' ) );
        }

        return $this->render( 'MESDFileDocument_upload'
            , array(
                'form' => $form->createView()
            ) );
    }
}
