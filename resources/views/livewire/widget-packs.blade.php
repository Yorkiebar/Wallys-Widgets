<div>
    {{-- Stop trying to control. --}}
    <div class="text-right">
        <x-button wire:click="openAddNewPack()">Add Pack</x-button>
    </div>
    <table class='mt-4'>
        <thead>
            <tr>
                <th>Amount of Widgets</th>
                <th>Currently Available</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($widgetPacks as $widgetPack)
                <tr>
                    <td>{!! $widgetPack->amount !!}</td>
                    <td>{!! $widgetPack->deleted_at ? 'No' : 'Yes' !!}</td>
                    <td><i class="fa fa-regular fa-pen-to-square" wire:click="editPack({!! $widgetPack->id !!})"></i></td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No packs currently available...</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <br/>
    {!! $widgetPacks->links() !!}

    @once
        <x-dialog-modal wire:model.live="editing">
            <x-slot name="title">
                {!! $editing_id ? 'Editing' : 'Creating' !!} Widget Pack
            </x-slot>

            <x-slot name="content">
                <div class="mt-4 half-block">
                    <x-label for="editing_amount">Widgets in Pack</x-label>
                    @if($editing_id && $editing_used_in_orders)
                        <x-input type="number" min=1 class="mt-1 block w-3/4" placeholder="{{ __('10') }}" 
                                    wire:model="editing_amount"
                                    disabled />
                        <p class="small-note w-3/4">This pack is used in an order and can not be edited</p>
                    @else
                        <x-input type="number" min=1 class="mt-1 block w-3/4" placeholder="{{ __('10') }}" 
                                    wire:model="editing_amount" />
                    @endif
                    <x-input-error for="editing_amount" class="mt-2" />
                </div>
                <div class="mt-4 half-block">
                    <x-label for="editing_available">Currently Available?</x-label>
                    <x-input type="checkbox" class="mt-1 block" 
                                wire:model="editing_available" />
                    <x-input-error for="editing_available" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-button class="ms-3" dusk="confirm-password-button" wire:click="store" wire:loading.attr="disabled">
                    Save
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endonce
</div>
