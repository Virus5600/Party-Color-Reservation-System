<div class="m-auto">
	@if ($paginator->hasPages())
		@php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)
	@endif
		
	<nav>
		<ul class="pagination m-auto">
			@if ($paginator->hasPages())
				{{-- Previous Page Link --}}
				@if ($paginator->onFirstPage())
					<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
						<span class="page-link" aria-hidden="true"><i class="fas fa-caret-left"></i></span>
					</li>
				@else
					<li class="page-item">
						<button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" aria-label="@lang('pagination.previous')"><i class="fas fa-caret-left"></i></button>
					</li>
				@endif

				{{-- Pagination Elements --}}
				@foreach ($elements as $element)
					{{-- "Three Dots" Separator --}}
					@if (is_string($element))
						<li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
					@endif

					{{-- Array Of Links --}}
					@if (is_array($element))
						@foreach ($element as $page => $url)
							@if ($page == $paginator->currentPage())
								<li class="page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}" aria-current="page"><span class="page-link">{{ $page }}</span></li>
							@else
								<li class="page-item" wire:key="paginator-{{ $paginator->getPageName() }}-{{ $this->numberOfPaginatorsRendered[$paginator->getPageName()] }}-page-{{ $page }}"><button type="button" class="page-link" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">{{ $page }}</button></li>
							@endif
						@endforeach
					@endif
				@endforeach

				{{-- Next Page Link --}}
				@if ($paginator->hasMorePages())
					<li class="page-item">
						<button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')"><i class="fas fa-caret-right"></i></button>
					</li>
				@else
					<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
						<span class="page-link" aria-hidden="true"><i class="fas fa-caret-right"></i></span>
					</li>
				@endif
			@else
				{{-- Previous Page Link --}}
				@if ($paginator->onFirstPage())
					<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
						<span class="page-link" aria-hidden="true"><i class="fas fa-caret-left"></i></span>
					</li>
				@else
					<li class="page-item">
						<button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" aria-label="@lang('pagination.previous')"><i class="fas fa-caret-left"></i></button>
					</li>
				@endif

				{{-- Pagination Elements --}}
				<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>

				{{-- Next Page Link --}}
				@if ($paginator->hasMorePages())
					<li class="page-item">
						<button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" aria-label="@lang('pagination.next')"><i class="fas fa-caret-right"></i></button>
					</li>
				@else
					<li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
						<span class="page-link" aria-hidden="true"><i class="fas fa-caret-right"></i></span>
					</li>
				@endif
			@endif
		</ul>
	</nav>
</div>