@props(['percentage'])

<div class="relative w-12 h-12">
    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
        <path
            d="M18 2.0845
               a 15.9155 15.9155 0 0 1 0 31.831
               a 15.9155 15.9155 0 0 1 0 -31.831"
            fill="none"
            stroke="#e5e7eb"
            stroke-width="3.8"
        />
        <path
            d="M18 2.0845
               a 15.9155 15.9155 0 0 1 0 31.831"
            fill="none"
            stroke="#3b82f6"
            stroke-width="3.8"
            stroke-dasharray="{{ $percentage }}, 100"
        />
    </svg>
    <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-700">
        {{ $percentage }}%
    </div>
</div>
