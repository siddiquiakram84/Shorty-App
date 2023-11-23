<x-app-layout>
    <head>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    </head>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('urls.update', $url) }}">
            @csrf
            @method('patch')

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-600">{{ __('Title') }}</label>
                <input type="text"
                    id="title"
                    name="title"
                    required
                    maxlength="255"
                    placeholder="{{ __('Enter a title') }}"
                    class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring focus:border-indigo-300"
                    value="{{ old('title', $url->title) }}"
                />
                <x-input-error :messages="$errors->store->get('title')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label for="original_url" class="block text-sm font-medium text-gray-600">{{ __('Original URL') }}</label>
                <input type="text"
                    id="original_url"
                    name="original_url"
                    required
                    maxlength="255"
                    placeholder="{{ __('Enter the original URL') }}"
                    class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring focus:border-indigo-300"
                    value="{{ old('original_url', $url->original_url) }}"
                />
                <x-input-error :messages="$errors->store->get('original_url')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('urls.index') }}" class="text-gray-500 ml-4">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
