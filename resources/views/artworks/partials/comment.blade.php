<div class="comment-item" style="margin-bottom:8px">
    @php
    $user = $comment->user; // relasi user
    $userId = $user->user_id ?? null;

    // Nama ditampilkan = USERNAME
    $namaUser = $user->username ?? 'User';

    // Apakah komentator adalah pembuat artwork?
    $isCreator = isset($artwork) && $userId === $artwork->user_id;

    // cuplikan komentar untuk reply
    $cuplikan = \Illuminate\Support\Str::limit($comment->isi_komentar ?? '', 120);

    // normalisasi tanggal
    $tgl = $comment->tanggal instanceof \Illuminate\Support\Carbon
    ? $comment->tanggal
    : ($comment->tanggal ? \Carbon\Carbon::parse($comment->tanggal) : null);

    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $canDelete = auth()->check() && (auth()->id() === (int)$comment->user_id || $isAdmin);
    $isOwner = auth()->check() && auth()->id() === (int)$comment->user_id;
    @endphp

    {{-- BARIS UTAMA: USERNAME + LABEL + ISI KOMENTAR --}}
    <div>
        @if($userId)
        {{-- Username sebagai link ke profil --}}
        <a href="{{ route('profiles.show', ['user' => $userId]) }}"
            class="comment-user-link"
            style="color:#f5f4ff; font-weight:600; text-decoration:none;">
            <span class="comment-user">{{ $namaUser }}</span>
        </a>

        {{-- Label pembuat --}}
        @if($isCreator)
        <span style="color:#ffd27f; font-size:.75rem; margin-left:4px;">
            (pembuat)
        </span>
        @endif

        :
        @else
        {{-- fallback tanpa link --}}
        <span class="comment-user">
            {{ $namaUser }}
            @if($isCreator)
            <span style="color:#ffd27f; font-size:.75rem; margin-left:4px;">
                (pembuat)
            </span>
            @endif
            :
        </span>
        @endif

        <span class="comment-text">{{ $comment->isi_komentar }}</span>
    </div>

    {{-- BARIS META: TANGGAL + AKSI --}}
    <div style="font-size:.72rem;color:#9aa; margin-left:6px;">
        {{ $tgl ? $tgl->translatedFormat('d M Y H:i') : '' }}

        @auth
        •
        <a href="#reply"
            class="reply-link"
            data-id="{{ $comment->comment_id }}"
            data-name="{{ $namaUser }}"
            data-text="{{ $cuplikan }}">
            Balas
        </a>

        @if($canDelete)
        •
        <a href="#hapus"
            class="comment-delete"
            data-id="{{ $comment->comment_id }}">
            Hapus
        </a>

        <form id="del-{{ $comment->comment_id }}"
            action="{{ route('comments.destroy', ['comment' => $comment->comment_id]) }}"
            method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endif

        {{-- TANDAI KOMENTAR – KHUSUS ADMIN --}}
        @if($isAdmin)
        •
        <a href="#report"
            onclick="event.preventDefault(); if(confirm('Tandai komentar ini untuk moderasi?')) document.getElementById('report-{{ $comment->comment_id }}').submit();">
            Tandai
        </a>

        <form id="report-{{ $comment->comment_id }}"
            action="{{ route('comments.report', $comment->comment_id) }}"
            method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="alasan" value="">
        </form>
        @endif
        @endauth
    </div>

    @if($isOwner || $isAdmin)
    @if($comment->status === 'ditandai')
    <div class="text-xs text-yellow-400 ml-1 mt-1">
        Komentar ini sedang dalam peninjauan admin.
    </div>
    @endif

    @if($comment->status === 'ditolak')
    <div class="text-xs text-red-400 ml-1 mt-1">
        Komentar ini telah dinyatakan melanggar dan disembunyikan dari pengguna lain.
    </div>
    @endif

    @if($comment->status === 'disetujui')
    <div class="text-xs text-blue-400 ml-1 mt-1">
        Komentar ini telah disetujui oleh admin.
    </div>
    @endif
    @endif


    {{-- REPLY (CHILD COMMENTS) --}}
    @if($comment->children && $comment->children->count())
    <div style="margin-left:14px; border-left:1px solid rgba(255,255,255,.12);
                    padding-left:8px; margin-top:6px;">
        @foreach($comment->children as $child)
        @include('artworks.partials.comment', ['comment' => $child, 'artwork' => $artwork])
        @endforeach
    </div>
    @endif
</div>