<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product as CrudModel;

class ProductController extends Controller
{
	public static $rules = [
		'name' => ['required', 'string', 'max:50']
	];
	public static $message = [];
	public static $attributes = [];

	public function index(Request $request)
	{
		$rules = [
			'query' => ['nullable', 'string'],
			'page' => ['nullable', 'numeric'],
			'per_page' => ['nullable', 'numeric']
		];

		$validateData = $request->validate($rules, self::$message, self::$attributes);	
		return CrudModel::search($validateData['query'] ?? '')
			->paginate($validateData['per_page'] ?? 15);
	}

	public function store(Request $request)
	{
		$validatedata = $request->validate(self::$rules, self::$message, self::$attributes);
		try{
			CrudModel::create($validatedata);
			return Response()->json(['message' => __('Store Success')]);	
		} catch(\Exception $e) {
			return Response()->json(['message' => $e->getMessage()], 422);
		}
	}
}
