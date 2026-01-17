<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormsController extends Controller
{
    public function download($filename)
    {
        $allowedFiles = [
            'NeoProLab_Couriers_BAA.pdf',
            'NeoProLab_Couriers_Rate_Sheet.pdf',
            'NeoProLab_Chain_of_Custody_Form.pdf',
            'NeoProLab_Specimen_Transport_Forms_and_Proposal.pdf'
        ];

        if (!in_array($filename, $allowedFiles)) {
            abort(404);
        }

        $path = storage_path('app/public/forms/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}