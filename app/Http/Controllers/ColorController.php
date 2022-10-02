<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color as CrudModel;

class ColorController extends Controller
{
	public function index(Request $request)    
	{
		return CrudModel::->paginate();
	}

	public function store(Request $request)
	{
		CrudModel::create();
		return Response()->json(['message' => __('Store Success')]);
	}
}
