<?php

namespace App\Controller;

use App\Broker\ExportConverter\ExportConverterRegistry;
use App\Entity\ExportExportUpload;
use App\Exception\BrokerDoesNotExistException;
use App\Form\ExportUploadForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private ExportConverterRegistry $brokerRegistry;

    public function __construct(ExportConverterRegistry $brokerRegistry)
    {
        $this->brokerRegistry = $brokerRegistry;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $upload = new ExportExportUpload();
        $form = $this->createForm(ExportUploadForm::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            try {
                $broker = $this->brokerRegistry->get($upload->getBroker());
                $mappedExport = $broker->convertExport($file);
                return new BinaryFileResponse($mappedExport->getStream());
            } catch (BrokerDoesNotExistException $e) {
                throw new NotFoundHttpException($e->getMessage());
            }
        }

        return $this->renderForm('index.html.twig', [
            'form' => $form
        ]);
    }
}
