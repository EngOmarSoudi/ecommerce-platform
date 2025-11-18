<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class BrandGuidelinesController extends Controller
{
    public function pdf(): Response
    {
        if (class_exists('Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $html = view('brand.guidelines')->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="brand-guidelines.pdf"',
            ]);
        }

        // Fallback to HTML download if PDF library is not installed
        return response(view('brand.guidelines'), 200, [
            'Content-Type' => 'text/html',
        ]);
    }
}
