<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Audit;
use DataTables;
use Exception;

class AuditController extends Controller
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
			'sort' => ['nullable', 'string'],       
			'sort_by' => ['nullable', 'string'],   			  
		];

		$validatedData = $request->validate($rules, self::$messages, self::$attributes);

        return Audit::where([
            'table' => $request->table,
            'table_id' => $request->table_id,
        ])
        ->orderBy($validatedData['sort'] ?? 'created_at', $this->sortBy($validatedData))
        ->paginate($validatedData['per_page'] ?? 15);
    }
}
