<div>
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        {{-- Mobile View --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#94a3b8] bg-[#0F172A] border border-[#1e293b] cursor-not-allowed rounded-xl">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#e2e8f0] bg-[#0F172A] border border-[#1e293b] rounded-xl hover:text-cyan-400 hover:bg-[#1e293b] transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500/20">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-[#e2e8f0] bg-[#0F172A] border border-[#1e293b] rounded-xl hover:text-cyan-400 hover:bg-[#1e293b] transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500/20">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-[#94a3b8] bg-[#0F172A] border border-[#1e293b] cursor-not-allowed rounded-xl">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-[#94a3b8] leading-5">
                    Menampilkan
                    <span class="font-medium text-[#e2e8f0]">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-medium text-[#e2e8f0]">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="font-medium text-[#e2e8f0]">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-xl gap-1.5 shadow-sm">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-[#334155] bg-[#0F172A] border border-[#1e293b] cursor-not-allowed rounded-xl" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        </span>
                    @else
                        <button wire:click="previousPage" dusk="previousPage.before" class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-[#e2e8f0] bg-[#0F172A] border border-[#1e293b] rounded-xl hover:text-cyan-400 hover:bg-[#1e293b] transition-colors focus:z-10 focus:outline-none focus:ring-2 focus:ring-cyan-500/20" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#334155] bg-[#0F172A] border border-[#1e293b] cursor-not-allowed rounded-xl">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-cyan-500 border border-cyan-500 cursor-default rounded-xl shadow-[0_0_15px_rgba(34,211,238,0.4)] z-10">{{ $page }}</span>
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#e2e8f0] bg-[#0F172A] border border-[#1e293b] rounded-xl hover:text-cyan-400 hover:bg-[#1e293b] transition-colors focus:z-10 focus:outline-none focus:ring-2 focus:ring-cyan-500/20" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" dusk="nextPage.before" class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-[#e2e8f0] bg-[#0F172A] border border-[#1e293b] rounded-xl hover:text-cyan-400 hover:bg-[#1e293b] transition-colors focus:z-10 focus:outline-none focus:ring-2 focus:ring-cyan-500/20" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2.5 py-2 text-sm font-medium text-[#334155] bg-[#0F172A] border border-[#1e293b] cursor-not-allowed rounded-xl" aria-hidden="true">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
</div>
