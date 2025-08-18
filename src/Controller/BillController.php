<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BillController extends AbstractController
{
    #[Route('/admin/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');
        $domPdf = new Dompdf($pdfOptions);

        $html = $this->renderView('bill/index.html.twig', [
            'order' => $order,
        ]);

        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        // --- THIS IS THE CORRECTED LOGIC ---

        // 1. Generate the PDF output as a string.
        $pdfOutput = $domPdf->output();

        // 2. Create a new Symfony Response object and set the PDF content.
        $response = new Response($pdfOutput);

        // 3. Set the appropriate headers to tell the browser this is a PDF.
        $response->headers->set('Content-Type', 'application/pdf');

        // The 'inline' disposition tells the browser to display the file, not download it.
        // We also set a filename for when the user decides to save it.
        $response->headers->set(
            'Content-Disposition',
            'inline; filename="Facture-' . $order->getId() . '.pdf"'
        );

        // 4. Return the fully-formed Response object.
        return $response;
    }
}
