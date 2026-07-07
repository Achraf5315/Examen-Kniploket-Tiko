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
            {{--
                Happy path: groene succesmelding na toevoegen/wijzigen/verwijderen.
                De 'transition-opacity duration-500' klasse laat het blok soepel uitfaden;
                het JavaScript onderaan start die fade na 4 seconden.
            --}}
            @if (session('success'))
                <div id="successFlashMessage" class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800 transition-opacity duration-500">
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
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Opmerking</th>
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
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $behandeling->Opmerking ?: '-' }}</td>
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
                                        Er zijn momenteel geen behandelingen beschikbaar.
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

            <form id="deleteForm" method="POST" class="mt-4 space-y-3" novalidate>
                @csrf
                @method('DELETE')
                <div id="deleteModalError" class="hidden rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></div>
                <input
                    type="text"
                    name="bevestigingscode"
                    id="bevestigingscode"
                    required
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
            const errorBox = document.getElementById('deleteModalError');

            form.action = button.getAttribute('data-delete-url');
            naamSpan.textContent = button.getAttribute('data-behandeling-naam');
            codeInput.value = '';
            codeInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            errorBox.textContent = '';
            errorBox.classList.add('hidden');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Sluit de modal en reset visuele status.
        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const errorBox = document.getElementById('deleteModalError');
            const codeInput = document.getElementById('bevestigingscode');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
            codeInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        }

        // Unhappy path (verwijderen): het formulier heeft 'novalidate', zodat de standaard
        // HTML5-browserballon niet verschijnt. We vangen de submit zelf af en tonen bij een
        // verkeerde invoer een rode foutmelding BINNEN de modal in plaats van te versturen.
        document.getElementById('deleteForm').addEventListener('submit', function (event) {
            const codeInput = document.getElementById('bevestigingscode');
            const errorBox = document.getElementById('deleteModalError');
            const ingevoerd = (codeInput.value || '').trim();

            if (ingevoerd !== 'VERWIJDEREN') {
                event.preventDefault();

                errorBox.textContent = 'De ingevoerde bevestigingscode is onjuist.';
                errorBox.classList.remove('hidden');
                codeInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                codeInput.focus();
                return;
            }

            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        });

        // Laat succesmeldingen automatisch na 4 seconden vloeiend verdwijnen.
        const successFlashMessage = document.getElementById('successFlashMessage');
        if (successFlashMessage) {
            setTimeout(function () {
                successFlashMessage.style.opacity = '0';
                setTimeout(function () {
                    successFlashMessage.remove();
                }, 500);
            }, 4000);
        }
    </script>
</x-app-layout>
