<x-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Attributes</h1>
                <p class="mt-1 text-sm text-gray-500">Manage product attributes and their options.</p>
            </div>
            <a href="{{ route('admin.attributes.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Attribute
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">Name</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider hidden sm:table-cell">Code</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider hidden md:table-cell">Value Type</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider hidden md:table-cell">Options</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-700 text-xs uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attributes as $attribute)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('admin.attributes.edit', $attribute) }}"
                                       class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                                        {{ $attribute->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-sm hidden sm:table-cell">{{ $attribute->code }}</td>
                                <td class="px-5 py-3 hidden md:table-cell">
                                    <span class="text-sm capitalize">{{ str_replace('_', ' ', $attribute->value_type) }}</span>
                                </td>
                                <td class="px-5 py-3 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $attribute->options_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}"
                                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">Edit</a>
                                        <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST"
                                              onsubmit="return confirm('Delete this attribute?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs font-medium text-red-600 hover:text-red-800 transition-colors">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                                    <p class="text-sm">No attributes found.</p>
                                    <a href="{{ route('admin.attributes.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mt-1 inline-block">Create your first attribute</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $attributes->links() }}
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-admin-layout>
