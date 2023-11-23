<x-app-layout>
    <div class="bg-white px-6 py-8" style="min-height: calc(100vh - 65px);">
        <form method="POST" action="{{ route('urls.store') }}" class="mb-6">
            @csrf
            <div class="flex items-end space-x-2">
                <div class="flex-1 pr-2">
                    <input type="text"
                        name="title"
                        required
                        maxlength="255"
                        placeholder="{{ __('Title') }}"
                        class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2"
                        value="{{ old('title') }}"
                    />
                    <x-input-error :messages="$errors->store->get('title')" class="mt-2" />
                </div>
                <div class="flex-1 pr-2">
                    <input type="text"
                        name="original_url"
                        required
                        maxlength="255"
                        placeholder="{{ __('Original Url') }}"
                        class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm p-2"
                        value="{{ old('original_url') }}"
                    />
                    <x-input-error :messages="$errors->store->get('original_url')" class="mt-2" />
                </div>
                <div>
                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </div>
            </div>
        </form>

        <table class="w-[100%] bg-white shadow-sm rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4">User</th>
                    <th class="py-2 px-4">Created At</th>
                    <th class="py-2 px-4">Title</th>
                    <th class="py-2 px-4">Original Url</th>
                    <th class="py-2 px-4">Shortener Url</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($urls as $item)
                    @if ($item->user->is(auth()->user()))
                        <tr>
                            <td class="py-2 px-4">
                                <div>
                                    <span class="font-bold">{{ $item->user->name }}</span>
                                </div>
                                
                            <td>
                                <div class="text-sm text-gray-600">
                                    {{ $item->created_at->format('j M Y, g:i a') }}
                                    @unless ($item->created_at->eq($item->updated_at))
                                        &middot; {{ __('edited') }}
                                    @endunless
                                </div>
                            </td>
                            <td class = "py-2 px-4">{{ $item->title }}</td>
                            <td class = "py-2 w-full px-4 ">
                                <a class = "text-blue-500 overflow-auto break-all hover:underline" href="{{ $item->original_url }}" target="_blank" >
                                    {{ $item->original_url }}
                                </a>
                            </td>
                            
                            <td class="py-2 px-4">
                                <a class = "text-blue-500 hover:underline" href="{{ route('shortener-url', $item->shortener_url) }}" target="_blank">
                                    {{ route('shortener-url', $item->shortener_url) }}
                                </a>
                            </td>
                            <td class="py-2 px-4">
                                @if ($item->user->is(auth()->user()))
                                    <x-dropdown>
                                        <x-slot name="trigger">
                                            <button class="text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link :href="route('urls.edit', $item)">
                                                {{ __('Edit') }}
                                            </x-dropdown-link>
                                            <form method="POST" action="{{ route('urls.destroy', $item) }}">
                                                @csrf
                                                @method('delete')
                                                <x-dropdown-link :href="route('urls.destroy', $item)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                    {{ __('Delete') }}
                                                </x-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
