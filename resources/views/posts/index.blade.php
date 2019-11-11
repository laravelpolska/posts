<ul>
    @foreach ($posts as $post)
        <li>
            <a href="/posts/{{ $post->id }}">
                {{ $post->title }}
            </a>
        </li>
    @endforeach
</ul>
