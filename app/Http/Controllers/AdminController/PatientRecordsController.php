<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PatientRecordsController extends Controller
{
    public function showpatientrecords()
    {
        return view('admin.patientrecords');
    }
}
