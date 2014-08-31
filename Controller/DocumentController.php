<?php

namespace Mesd\FileDocumentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mesd\FileDocumentBundle\Entity\Document;
use Mesd\FileDocumentBundle\FormType\DocumentType;

/**
 * Document Controller
 *
 * Handles all file uploads and downloads
 *
 * @package   Mesd\FileDocumentBundle\Controller
 * @author    Dave Lighthart  <dlighthart@mesd.k12.or.us>
 * @author    Curtis G Hanson <chanson@mesd.k12.or.us>
 * @copyright 2014 MESD
 * @license   <http://opensource.org/licenses/MIT> MIT
 * @version   0.1
 */
class DocumentController extends Controller
{
    /**
     * Upload Action
     *
     * Renders upload form
     * 
     * @param  Request $request The request object
     * @return string  $twig    Html upload form
     */
    public function uploadAction(Request $request)
    {
        $document = $this->get('document');

        $form = $this->createForm(new DocumentType(), $document);

        return $this->render('MesdFileDocumentBundle:Document:upload.html.twig',
            array(
                'form' => $form->createView(),
       ));
    }

    public function receiveUploadAction(Request $request)
    {
        $document = $this->get('document');

        $form = $this->createForm(new DocumentType(), $document);

        $form->bindRequest($request);

        if ($form->isValid()) {
            if ($document->getFilename() === null) {
                $document->setFilename($document->getFile()->getClientOriginalName());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('default'));
        }

        return $this->render('MesdFileDocument_upload', array(
            'form' => $form->createView()
        ));
    }

    public function downloadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository('MesdFileDocumentBundle:Document')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find the document');
        }

        $headers = array(
            'Content-Type'        => $document->getMimeType() ? : 'file',
            'Content-Disposition' => 'attachment; filename="' . $document->getFilename() . '"'
        );

        $filename = $document->getPath() . '/' . $document->getHash();

        return new Response(file_get_contents($filename), 200, $headers);
    }

    /**
     * Get Upload Progress
     *
     * Get the progress of a download or downloads.
     * A returned array looks like this:
     * 
     *     $_SESSION["upload_progress_123"] = array(
     *         "start_time"      => 1234567890,   // the request time
     *         "content_length"  => 57343257,     // POST content length
     *         "bytes_processed" => 453489,       // amt of bytes received and processed
     *         "done"            => false,        // true when the POST handler has finished, successfully or not
     *         "files"           => array(
     *              0 => array(
     *                  "field_name"      => "file1",          // name of the <input/> field
     *                  "name"            => "foo.avi",
     *                  "tmp_name"        => "/tmp/phpxxxxxx",
     *                  "error"           => 0,
     *                  "done"            => true,             // true when the POST handler has finished handling file
     *                  "start_time"      => 1234567890,       // when file has started processing
     *                  "bytes_processed" => 57343250,         // no. of bytes received and processed for file
     *              ),
     *              // An other file, not finished uploading, in the same request
     *              1 => array(
     *                  "field_name"      => "file2",
     *                  "name"            => "bar.avi",
     *                  "tmp_name"        => NULL,
     *                  "error"           => 0,
     *                  "done"            => false,
     *                  "start_time"      => 1234567899,
     *                  "bytes_processed" => 54554,
     *              ),
     *          )
     *     );
     *
     * Remember though! Data returned is in json format, not a php array!
     * 
     * @return JsonResponse|mixed[] An array of upload progress data
     * @see    <http://php.net/manual/en/session.upload-progress.php>
     * @author Curtis G Hanson <chanson@mesd.k12.or.us>
     * @todo   Add functionality to choose how the data is returned; json or not
     */
    public function progressAction()
    {
        $request = $this->container->get('request');
        $session = $this->container->get('session');

        $prefix = ini_get('session.upload_progress.prefix');
        $name   = ini_get('session.upload_progress.name');

        // https://github.com/blueimp/jQuery-File-Upload/wiki/PHP-Session-Upload-Progress
        $key   = sprintf('%s.%s', $prefix, $request->get($name));
        $value = $session->get($key);

        $progress = array(
            'lengthComputable' => true,
            'loaded'           => $value['bytes_processed'],
            'total'            => $value['content_length'],
        );

        return new JsonResponse($progress);
    }
}
