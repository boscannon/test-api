<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category as CrudModel;

class CategoryController extends Controller
{
	public static $rules = [
		'name' => ['required', 'string', 'max:50']
	];
	public static $messages = [];
	public static $attributes = [];

	public function index(Request $request)
	{
		$rules = [            
			'query' => ['nullable', 'string'],
			'page' => ['nullable', 'numeric'],
			'per_page' => ['nullable', 'numeric'],       
		];

		$validatedData = $request->validate($rules, self::$messages, self::$attributes);

		return CrudModel::search($validatedData['query'] ?? '')
			->paginate($validatedData['per_page'] ?? 15);
	}

	public function store(Request $request)
	{
		$validatedData = $request->validate(self::$rules, self::$messages, self::$attributes);
		try {
			CrudModel::create($validatedData);
			return Response()->json(['message' => __('Store Success')]);
		} catch(\Exception $e) {
			return Response()->json(['message' => $e->getMessage()], 422);
		}
	}
}
