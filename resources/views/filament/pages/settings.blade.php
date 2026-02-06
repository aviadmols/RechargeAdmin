<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Recharge API</x-slot>
            <div class="space-y-4">
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium">API Token</span>
                    </label>
                    <input type="password" wire:model="token" placeholder="Leave blank to keep current"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium">Base URL</span>
                    </label>
                    <input type="url" wire:model="base_url" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium">API Version</span>
                    </label>
                    <input type="text" wire:model="api_version" placeholder="2021-11"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium">Store domain</span>
                    </label>
                    <input type="text" wire:model="store_domain"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Feature toggles</x-slot>
            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="enable_cancel" class="rounded border-gray-300">
                    <span class="text-sm">Allow cancel</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="enable_swap" class="rounded border-gray-300">
                    <span class="text-sm">Allow swap variant</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="enable_pause" class="rounded border-gray-300">
                    <span class="text-sm">Allow pause/resume</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="enable_address_update" class="rounded border-gray-300">
                    <span class="text-sm">Allow address update</span>
                </label>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Cache (seconds)</x-slot>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Orders TTL</label>
                    <input type="number" wire:model="cache_ttl_orders" min="0"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Subscriptions TTL</label>
                    <input type="number" wire:model="cache_ttl_subscriptions" min="0"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </x-filament::section>

        <div class="flex gap-2">
            <x-filament::button type="submit">Save</x-filament::button>
            <x-filament::button color="gray" wire:click="testConnection" type="button">Test connection</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
