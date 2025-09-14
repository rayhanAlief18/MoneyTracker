<div class="p-6">
    <div class="mb-4">
        <img src="{{ asset('storage/'.$record->bukti_bayar) }}" alt="">
    </div>
    <div class="mt-5">
        <h3 class="text-xl font-bold mb-4 mt-5">Deskripsi</h3>
        <p>{{ $record->debt_request->keterangan }}</p>
    </div>
</div>