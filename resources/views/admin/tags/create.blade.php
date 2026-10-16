<x-admin-layout>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.tags.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <nav class="text-xs text-gray-400 mb-0.5">
                <a href="{{ route('admin.tags.index') }}" class="hover:text-gray-600 transition-colors">Tags</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700">New tag</span>
            </nav>
            <h1 class="text-lg font-semibold text-gray-900 leading-tight">New tag</h1>
        </div>
    </div>

    <div class="max-w-xl">
        <form action="{{ route('admin.tags.store') }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                       placeholder="e.g. Summer Collection">
                @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1.5">The slug will be auto-generated from the name.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.tags.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors shadow-sm">
                    Create
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>