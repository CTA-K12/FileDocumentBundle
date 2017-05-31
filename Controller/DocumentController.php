<?php

namespace Mesd\FileDocumentBundle\Controller;

use Mesd\FileDocumentBundle\FormType\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function uploadAction(Request $request)
    {
        $document = $this->get('document');

        $form = $this->createForm(new DocumentType()
            , $document
        );

        return $this->render('MesdFileDocumentBundle:Document:upload.html.twig'
            , [
                'form' => $form->createView(),
            ]);
    }

    public function receiveUploadAction(Request $request)
    {
        $document = $this->get('document');

        $form = $this->createForm(new DocumentType()
            , $document
        );

        $form->bind($request);

        if ($form->isValid()) {
            if ($document->getFilename() === null) {
                $document->setFilename($document->getFile()->getClientOriginalName());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('default'));
        }

        return $this->render('MesdFileDocument_upload'
            , [
                'form' => $form->createView(),
            ]);
    }

    public function downloadAction(
        Request $request,
                $id
    ) {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository('MesdFileDocumentBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find the document');
        }

        $headers = [
            'Content-Type'        => $document->getMimeType() ?: 'file',
            'Content-Disposition' => 'attachment; filename="' . $document->getFilename() . '"',
        ];

        $filename = $document->getPath() . '/' . $document->getHash();

        return new Response(file_get_contents($filename), 200, $headers);
    }
}
