<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function whatsapp(Request $request): View { return view('panel.operations.whatsapp'); }
    public function reports(Request $request): View { return view('panel.operations.reports'); }
    public function qr(Request $request): View { return view('panel.operations.qr', ['restaurant'=>$request->user()->restaurant]); }
}