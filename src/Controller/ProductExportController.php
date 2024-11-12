<?php
// src/Controller/ProductExportController.php

namespace App\Controller;

use App\Repository\ProductRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductExportController extends AbstractController
{
    #[Route('/api/products/export', name: 'product_export', methods: ['GET'])]
    public function export(Request $request, ProductRepository $repository): Response
    {
        // Get filters and sorting for export
        $filters = $request->query->all();
        $sort = $request->query->get('sort', []);

        // Fetch the data (using the same repository as in the search)
        $qb = $repository->searchQueryBuilder($filters, $sort);
        $products = $qb->getQuery()->getResult();

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Price');
        $sheet->setCellValue('E1', 'Created At');

        // Add data rows
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->getId());
            $sheet->setCellValue('B' . $row, $product->getName());
            $sheet->setCellValue('C' . $row, $product->getCategory());
            $sheet->setCellValue('D' . $row, $product->getPrice());
            $sheet->setCellValue('E' . $row, $product->getCreatedAt()->format('Y-m-d H:i:s'));
            $row++;
        }

        // Write CSV or Excel
        $writer = new Csv($spreadsheet); // For CSV
        // $writer = new Xlsx($spreadsheet); // For Excel
        $filename = 'products_export_' . time() . '.csv'; // or .xlsx

        // Send the response
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->setContent($writer->save('php://output'));

        return $response;
    }
}
