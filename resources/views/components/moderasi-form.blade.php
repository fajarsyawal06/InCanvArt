<div class="mt-8 bg-white dark:bg-[#393053] rounded-xl shadow p-6 border border-gray-200 dark:border-[#635985]">

    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        Moderasi Konten
    </h2>

    <form action="{{ route('admin.moderations.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- TARGET --}}
        <input type="hidden" name="target_type" value="{{ $targetType }}">
        <input type="hidden" name="target_id" value="{{ $targetId }}">

        {{-- STATUS --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Keputusan Moderasi
            </label>

            <select name="status"
                    class="w-full rounded-lg px-3 py-2 text-sm border
                           bg-white dark:bg-[#18122B]
                           border-gray-300 dark:border-[#635985]
                           text-gray-900 dark:text-gray-100
                           focus:ring-indigo-500 focus:border-indigo-500">
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="ditandai" selected>Ditandai</option>
            </select>
        </div>

        {{-- ALASAN --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Alasan / Catatan
            </label>

            <textarea name="alasan" rows="4"
                      class="w-full rounded-lg px-3 py-2 text-sm border
                             bg-white dark:bg-[#18122B]
                             border-gray-300 dark:border-[#635985]
                             text-gray-900 dark:text-gray-100
                             focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Tuliskan alasan atau catatan moderasi..."></textarea>
        </div>

        {{-- BUTTON --}}
        <div class="pt-2 flex justify-end">
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium shadow">
                Simpan Moderasi
            </button>
        </div>

    </form>
</div>
