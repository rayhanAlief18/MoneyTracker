<x-filament::widget>
    <div class="flex flex-col justify-center h-full">
        

    
    <div class="flex flex-col gap-6">
        <div class="grid grid-cols-1 mb-6">
            <div class="bg-primary-500 text-white rounded-xl p-6 shadow flex flex-col justify-content-center items-center" >
                <div class="text-sm font-medium mb-1">Total Saldo</div>
                <div class="text-2xl font-bold">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</div>
                <div class="text-sm opacity-80 mt-1">Jumlah total saldo</div>
            </div>
        </div>

        {{-- Money Placing (2-3 columns per row) --}}
        <div class="flex flex-row flex-wrap justify-between gap-4 w-full">
            @foreach ($placements as $placing)
                <div class=" border-gray-200 rounded-xl p-4 shadow-sm mt-4 flex-1 min-w-[500px]" style="background-color: #18181B;">
                    <div class="text-sm font-medium text-gray-600 mb-1">{{ $placing->name }}</div>
                    <div class="text-xl font-bold text-black-800">Rp {{ number_format($placing->amount, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Saldo saat ini</div>
                </div>
            @endforeach
        </div>
    </div>

    </div>
</x-filament::widget>