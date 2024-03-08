<div>
    {{-- Do your work, then step back. --}}
    <div class="text-right">
        <x-button wire:click="openAddNewOrder()">Add Order</x-button>
    </div>
    <table class='mt-4'>
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount of Widgets</th>
                <th>Packs Used</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{!! $order->created_at->format('gS F Y H:ia') !!}</td>
                    <td>{!! $order->customer_name !!}</td>
                    <td>{!! $order->order_amount !!}</td>
                    <td>{!! $order->packsStr !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No orders currently available...</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <br/>
    {!! $orders->links() !!}

    @once
        <x-dialog-modal wire:model.live="editing">
            <x-slot name="title">
                Adding Order
            </x-slot>

            <x-slot name="content">
                <div class="mt-4 half-block">
                    <x-label for="editing_customer">Customer Name</x-label>
                    <x-input type="text" class="mt-1 block w-3/4" 
                                wire:model="editing_customer" />
                    <x-input-error for="editing_customer" class="mt-2" />
                </div>
                <div class="mt-4 half-block">
                    <x-label for="editing_amount">Amount of Widgets</x-label>
                    <x-input type="number" min=1 class="mt-1 block w-3/4" placeholder="{{ __('10') }}" 
                                wire:model="editing_amount" />
                    <x-input-error for="editing_amount" class="mt-2" />
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
