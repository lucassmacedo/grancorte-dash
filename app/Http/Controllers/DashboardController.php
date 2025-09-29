<?php

namespace App\Http\Controllers;

use App\Models\DashboardFaturamentoTotal;
use App\Models\LogisticaEntrega;
use App\Models\System\DashboardFaturamento;
use App\Models\System\DashboardRecebimento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('home');
    }
}
