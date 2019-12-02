<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->get();

        return view('posts.index')->with('posts', $posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => [
                'required',
                Rule::unique('posts'),
            ],
            'body' => [
                'nullable',
                'min:3',
            ],
        ]);

        $request->user()->posts()->create($request->only([
            'published_at',
            'title',
            'body',
        ]));
    }

    public function show(Post $post)
    {
        return view('posts.show')->with('post', $post);
    }
}
