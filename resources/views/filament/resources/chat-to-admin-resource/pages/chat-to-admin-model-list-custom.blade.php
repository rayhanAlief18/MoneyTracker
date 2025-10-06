<x-filament::page>
    <div class="flex items-center justify-between mb-6">
        <a href="{{ \App\Filament\Resources\ChatToAdminResource::getUrl('create') }}"
            class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-700 transition">
            + Tambah Baru
        </a>
    </div>

    {{-- Grid Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3  gap-4">
        @foreach ($chat as $chats)
            @if($chats['status'] === 'Belum Dibaca')
                <div x-data="{ open: false }" class="rounded-xl p-4 shadow-sm dark:bg-gray-800 ">
            @elseif($chats['status'] === 'Sudah Dibaca')
                    <div x-data="{ open: false }" class="rounded-xl p-4 shadow-sm dark:bg-primary-500 ">
                @endif
                    {{-- Header --}}
                    <div class="flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div>
                            <div
                                class="{{ $chats['status'] === 'Belum Dibaca' ? 'text-gray-400' : 'text-white' }}text-sm font-medium">
                                Chat {{ $loop->iteration }}</div>
                            <div class="text-md mt-2 font-bold text-white">{{ $chats['pesan'] }}</div>
                        </div>
                        <div>
                            @if($chats['status'] === 'Belum Dibaca')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @elseif($chats['status'] === 'Sudah Dibaca')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-10">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @endif
                            @if($chats['status'] === 'Belum Dibaca')
                                <svg :class="open ? 'rotate-180' : ''"
                                    class="w-5 h-10 text-gray-400 transition-transform duration-300 " fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            @elseif($chats['status'] === 'Sudah Dibaca')
                                <svg :class="open ? 'rotate-180' : ''"
                                    class="w-5 h-10 text-gray-400 transition-transform duration-300 " fill="none"
                                    stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif


                        </div>
                    </div>

                    {{-- Accordion Content --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 max-h-screen" x-transition:leave-end="opacity-0 max-h-0"
                        class="mt-4 overflow-hidden text-sm text-gray-300 pt-3">
                        <strong>Respon admin :</strong>
                        <p>{{ $chats['balasan'] }}</p>

                    </div>
                </div>
        @endforeach
        </div>
</x-filament::page>