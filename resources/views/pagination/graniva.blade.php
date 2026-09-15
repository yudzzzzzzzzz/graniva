@if ($paginator->hasPages())
<style>
    .graniva-pagination{display:flex;justify-content:center;margin:2.2rem 0 .5rem}
    .graniva-pagination ul{display:flex;gap:6px;list-style:none;margin:0;padding:0;align-items:center;flex-wrap:wrap;justify-content:center}
    .graniva-pagination li a,
    .graniva-pagination li span{
        display:flex;align-items:center;justify-content:center;
        min-width:42px;height:42px;padding:0 14px;
        border-radius:12px;border:1.5px solid #e5ded5;background:#fff;color:#495057;
        font-weight:700;font-size:.88rem;text-decoration:none;transition:.2s;
        box-shadow:0 2px 8px rgba(35,39,44,.05);
    }
    .graniva-pagination li a:hover{
        border-color:#b45a3c;color:#b45a3c;background:#fdf6f2;transform:translateY(-2px);
        box-shadow:0 6px 14px rgba(180,90,60,.18);
    }
    .graniva-pagination li.active span{
        background:linear-gradient(135deg,#b45a3c,#96482e);
        border-color:transparent;color:#fff;
        box-shadow:0 6px 16px rgba(180,90,60,.35);
    }
    .graniva-pagination li.disabled span{opacity:.45;cursor:not-allowed;background:#faf8f5}
    .graniva-pagination .page-info{
        font-size:.78rem;color:#9a928a;font-weight:600;margin-top:10px;text-align:center;width:100%;
    }
</style>
<nav class="graniva-pagination">
    <ul>
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span><i class="bi bi-chevron-left"></i></span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Halaman sebelumnya"><i class="bi bi-chevron-left"></i></a></li>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Berikutnya --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" title="Halaman berikutnya"><i class="bi bi-chevron-right"></i></a></li>
        @else
            <li class="disabled"><span><i class="bi bi-chevron-right"></i></span></li>
        @endif

        {{-- Info halaman --}}
        <li class="page-info">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            · {{ $paginator->total() }} data
        </li>
    </ul>
</nav>
@endif