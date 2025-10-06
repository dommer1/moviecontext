<ul class="hidden items-center gap-x-8 xl:flex">
    <li>
        <a href="{{ route('home') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('home') ? 'text-green-400' : 'hover:text-green-400' }}">Domov</a>
    </li>
    <li>
        <a href="{{ route('tag.show', 'recenzie') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('tag.show') && request()->route('slug') === 'recenzie' ? 'text-green-400' : 'hover:text-green-400' }}">Recenzie</a>
    </li>
    <li>
        <a href="{{ route('tag.show', 'novinky') }}" class="font-bold whitespace-nowrap transition-colors duration-150 {{ request()->routeIs('tag.show') && request()->route('slug') === 'novinky' ? 'text-green-400' : 'hover:text-green-400' }}">Novinky</a>
    </li>
    <li>
        <a href="#" class="font-bold whitespace-nowrap transition-colors duration-150 hover:text-green-400">Autori</a>
    </li>
</ul>
