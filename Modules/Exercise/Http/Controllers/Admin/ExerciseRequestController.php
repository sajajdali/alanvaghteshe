<?php

namespace Modules\Exercise\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Exercise\Entities\ExercisePlanRequest;
use TCPDF;
use TCPDF_FONTS;

class ExerciseRequestController extends Controller
{
    public function print(ExercisePlanRequest $exercise_plan_request): View
    {
        $this->authorize('update', $exercise_plan_request);

        $details = $exercise_plan_request->details()->orderBy('id')->get();
        return view('exercise::admin.print', compact('exercise_plan_request','details'));
    }

    public function pdf(ExercisePlanRequest $exercise_plan_request)
    {
        $this->authorize('update', $exercise_plan_request);

        //load HTML from render admin.print
        $details = $exercise_plan_request->details()->orderBy('id')->get();

        $html = view('exercise::admin.print', compact('exercise_plan_request','details'))->render();
        //add custom font to TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 018');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //write the HTML into the PDF
        try{
//        $fontname = TCPDF_FONTS::addTTFfont(public_path('assets/admin/fonts/ttf/IRANSansWeb.ttf'), 'IRANSans', '', 12);
            $pdf->setRTL(true);
            $pdf->setLanguageArray([
                'a_meta_charset' => 'UTF-8',
                'a_meta_dir' => 'rtl',
                'a_meta_language' => 'fa',
                'w_page' => 'page',
            ]);
//            $pdf->SetFont($fontname, '', 12);
            $pdf->SetFont('dejavusans', '', 11, '', true);

            $pdf->AddPage();
            $pdf->Ln();
            $pdf->writeHTML($html, true, false, true, false, '');
        }catch (\Exception $exception){
        }
        $pdf->Output('plan-'.$exercise_plan_request->id.'.pdf', 'D');

    }
}
