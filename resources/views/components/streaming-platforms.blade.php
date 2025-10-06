@props(['platforms' => []])

@if($platforms && count($platforms) > 0)
    <div class="bg-gray-100 p-6 rounded-lg">
        <h3 class="text-xl font-bold mb-4">Kde sledovať</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($platforms as $platform)
                @php
                    $affiliateLink = \App\Models\AffiliateLink::where('platform', $platform)->active()->first();
                @endphp

                @if($affiliateLink)
                    <a href="{{ $affiliateLink->url }}"
                       target="_blank"
                       rel="noopener sponsored"
                       class="flex flex-col items-center p-3 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 mb-2 bg-gray-200 rounded-lg flex items-center justify-center">
                            @if($affiliateLink->logo_path)
                                <img src="{{ asset('storage/' . $affiliateLink->logo_path) }}"
                                     alt="{{ $affiliateLink->name }}"
                                     class="w-8 h-8 object-contain">
                            @else
                                <span class="text-xs font-bold text-gray-600">{{ substr($affiliateLink->name, 0, 3) }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-center">{{ $affiliateLink->name }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endif
