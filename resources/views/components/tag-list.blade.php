@props(['tags', 'limit' => null, 'separator' => '•'])

@if($tags && $tags->count() > 0)
    <ul class="flex flex-wrap items-center gap-2">
        @foreach($tags->take($limit ?: $tags->count()) as $tag)
            <li>
                <x-tag-link :tag="$tag" />
            </li>
        @endforeach
    </ul>
@endif
