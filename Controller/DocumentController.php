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

    public function downloadAction( Request $request ) {
        $em = $this->getDoctrine()->getEntityManager();

        $document = $em->getRepository( 'MESDFileDocumentBundle:Document' )->find( $id );

        if ( !$document ) {
            throw $this->createNotFoundException( 'Unable to find the document' );
        }

        $headers = array(
            'Content-Type' => $document->getMimeType()
            'Content-Disposition' => 'attachment; filename="'.$document->getFilename().'"'
        );

        $filename = $document->getPath().'/'.$document->getFilename();

        return new Response( file_get_contents( $filename ), 200, $headers );
    }
}
