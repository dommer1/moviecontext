@props(['author', 'class' => ''])

<a href="{{ route('author.show', $author->slug) }}" class="{{ $class }} {{ $class ? '' : 'inline-block text-sm font-bold transition-colors duration-150 hover:text-green-400' }}">
    {{ $author->name }}
</a>
