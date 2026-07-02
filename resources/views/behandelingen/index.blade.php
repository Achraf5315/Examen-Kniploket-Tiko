<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Behandeling overzicht
            </h2>
            <a
                href="{{ route('behandelingen.create') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500"
            >
                Behandeling toevoegen
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold">Er is een validatiefout opgetreden:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Naam</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Prijs</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Duur (min)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Producten</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Actief</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($behandelingen as $behandeling)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $behandeling->Naam }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">EUR {{ number_format((float) $behandeling->Prijs, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $behandeling->DuurMinuten }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $behandeling->Producten ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ (int) $behandeling->IsActief === 1 ? 'Ja' : 'Nee' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="inline-flex items-center gap-2">
                                            <a
                                                href="{{ route('behandelingen.edit', $behandeling->Id) }}"
                                                class="rounded-md bg-amber-500 px-3 py-1.5 font-medium text-white hover:bg-amber-400"
                                            >
                                                Wijzigen
                                            </a>
                                            <button
                                                type="button"
                                                class="rounded-md bg-red-600 px-3 py-1.5 font-medium text-white hover:bg-red-500"
                                                data-delete-url="{{ route('behandelingen.destroy', $behandeling->Id) }}"
                                                data-behandeling-naam="{{ $behandeling->Naam }}"
                                                onclick="openDeleteModal(this)"
                                            >
                                                Verwijderen
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-300">
                                        Er zijn nog geen behandelingen gevonden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal voor verwijderbevestiging met verplichte code "VERWIJDEREN". -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900">Behandeling verwijderen</h3>
            <p class="mt-2 text-sm text-gray-700">
                Weet je zeker dat je <span id="modalBehandelingNaam" class="font-semibold"></span> wilt verwijderen?
            </p>
            <p class="mt-2 text-sm text-gray-700">
                Typ exact <span class="font-bold">VERWIJDEREN</span> om te bevestigen.
            </p>

            <form id="deleteForm" method="POST" class="mt-4 space-y-3">
                @csrf
                @method('DELETE')
                <input
                    type="text"
                    name="bevestigingscode"
                    id="bevestigingscode"
                    required
                    pattern="VERWIJDEREN"
                    title="Typ exact VERWIJDEREN"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="VERWIJDEREN"
                >
                <div class="flex items-center justify-end gap-2">
                    <button
                        type="button"
                        class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300"
                        onclick="closeDeleteModal()"
                    >
                        Annuleren
                    </button>
                    <button
                        type="submit"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                    >
                        Definitief verwijderen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Opent de modal en koppelt de juiste delete-url aan het formulier.
        function openDeleteModal(button) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const naamSpan = document.getElementById('modalBehandelingNaam');
            const codeInput = document.getElementById('bevestigingscode');

            form.action = button.getAttribute('data-delete-url');
            naamSpan.textContent = button.getAttribute('data-behandeling-naam');
            codeInput.value = '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Sluit de modal en reset visuele status.
        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-app-layout>
