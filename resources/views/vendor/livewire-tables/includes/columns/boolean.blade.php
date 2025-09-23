@if($isToggleable && $toggleMethod != '')
    <button class="btn btn-primary btn-sm" wire:click="{{ $toggleMethod }}('{{ $rowPrimaryKey }}')"
            @if($hasConfirmMessage) wire:confirm="{{ $confirmMessage }}" @endif
    >
        @endif
        @if ($status)
            @if ($type === 'icons')
                @if ($successValue === true)
                    <x-heroicon-o-check-circle
                        @class(
                            [
                                "inline-block h-5 w-5 text-green-500" => $isTailwind,
                                "d-inline-block text-success laravel-livewire-tables-btn-small" => $isBootstrap
                            ]
                        )
                    />
                @else
                    <x-heroicon-o-check-circle @class(
                        [
                            "inline-block h-5 w-5 text-red-500" => $isTailwind,
                            "d-inline-block text-danger laravel-livewire-tables-btn-small" => $isBootstrap
                        ]
                    )
                    />
                @endif
            @elseif ($type === 'yes-no')
                @if ($successValue === true)
                    <span>                    <i class="fa fa-thumbs-up"></i>                    Sim</span>
                @else
                    <span>                    <i class="fa fa-thumbs-down"></i>                    Não</span>
                @endif
            @endif
        @else
            @if ($type === 'icons')
                @if ($successValue === false)
                    <x-heroicon-o-x-circle @class(
                        [
                            "inline-block h-5 w-5 text-green-500" => $isTailwind,
                            "d-inline-block text-success laravel-livewire-tables-btn-small" => $isBootstrap
                        ]
                    )
                    />
                @else
                    <x-heroicon-o-x-circle @class(
                        [
                            "inline-block h-5 w-5 text-red-500" => $isTailwind,
                            "d-inline-block text-danger laravel-livewire-tables-btn-small" => $isBootstrap
                        ]
                    )
                    />
                @endif
            @elseif ($type === 'yes-no')
                @if ($successValue === false)
                    <span>                    <i class="fa fa-thumbs-up"></i>                    Sim</span>
                @else
                    <span>                    <i class="fa fa-thumbs-down"></i>                    Não</span>
                @endif
            @endif
        @endif
        @if($isToggleable && $toggleMethod != '')
    </button>
@endif
