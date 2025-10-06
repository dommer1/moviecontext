@props(['author', 'size' => 'sm', 'showSpecialization' => true])

<div class="flex items-center gap-4">
    <x-author-link :author="$author">
        <div class="block shrink-0">
            <div class="w-{{ $size === 'lg' ? '14' : ($size === 'md' ? '12' : '10') }} h-{{ $size === 'lg' ? '14' : ($size === 'md' ? '12' : '10') }} bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-{{ $size === 'lg' ? 'sm' : 'xs' }} font-bold">{{ substr($author->name, 0, 2) }}</span>
            </div>
        </div>
    </x-author-link>

    <div class="space-y-1.5">
        <x-author-link :author="$author" class="block text-{{ $size === 'lg' ? 'sm' : 'xs' }} font-bold leading-none transition-colors duration-150 hover:text-green-400" />

        @if($showSpecialization && $author->specialization)
            <div class="text-{{ $size === 'lg' ? 'xs' : 'xs' }} text-gray-600">{{ ucfirst($author->specialization) }}</div>
        @endif
    </div>
</div>
