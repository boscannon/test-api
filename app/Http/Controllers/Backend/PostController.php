<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules = [            
            'query' => ['nullable', 'string'],
            'page' => ['nullable', 'numeric'],
            'per_page' => ['nullable', 'numeric'],       
            'sort' => ['nullable', 'string'],       
            'sort_by' => ['nullable', 'string'],       
        ];

        $messages = [];

        $attributes = [];

        $validatedData = $request->validate($rules, $messages, $attributes);

        return Post::search($validatedData['query'] ?? '')
            ->orderBy($validatedData['sort'] ?? 'created_at', $this->sortBy($validatedData))
            ->paginate($validatedData['per_page'] ?? 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Post::create($request->all());
        return ['message' => __('backend/message.Success', ['action' => __('backend/message.Create')])];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $post->update($request->all());
        return ['message' => __('backend/message.Success', ['action' => __('backend/message.Edit')])];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();        
        return ['message' => __('backend/message.Success', ['action' => __('backend/message.Delete')])];
    }
}
