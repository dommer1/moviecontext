@props(['tag', 'class' => ''])

<a href="{{ route('tag.show', $tag->slug) }}" class="{{ $class }} {{ $class ? '' : 'category-badge' }}">
    {{ $tag->name }}
</a>
