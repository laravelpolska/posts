<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->get();

        return view('posts.index')->with('posts', $posts);
    }

    public function store(PostRequest $request)
    {
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

    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->only([
            'published_at',
            'title',
            'body',
        ]));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();
    }
}
