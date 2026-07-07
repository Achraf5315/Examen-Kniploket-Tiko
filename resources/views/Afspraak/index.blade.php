{{-- Afspraakoverzicht conform de wireframe: titel, knop "Nieuwe afspraak", filterbalk en tabel --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Afspraken
            </h2>
            @if (Route::has('afspraken.create'))
                <a href="{{ route('afspraken.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">
                    Nieuwe afspraak
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('succes'))
                <div id="successFlashMessage" class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800 transition-opacity duration-500">
                    {{ session('succes') }}
                </div>
            @endif

            @if (session('fout'))
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('fout') }}
                </div>
            @endif

            {{-- Foutmelding wanneer het overzicht niet uit de database geladen kan worden --}}
            @isset($foutmelding)
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ $foutmelding }}
                </div>
            @endisset

            {{-- Filterbalk: filtert op klant, medewerker, behandeling, datum of status --}}
            <form method="GET" action="{{ route('afspraken.index') }}" class="mb-4 flex flex-wrap items-center gap-2" role="search">
                <input type="text" name="zoek" value="{{ $zoekterm }}" placeholder="Filter afspraken..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-64">
                <button type="submit" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300">
                    Filter
                </button>
            </form>

            {{-- Tabel met alle afspraken uit de stored procedure spAfspraakOverzicht --}}
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Klant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Medewerker</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Behandeling</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Datum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Tijd</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-200">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            @forelse ($afspraken as $afspraak)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $afspraak->KlantNaam }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $afspraak->MedewerkerNaam }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $afspraak->BehandelingNaam }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Carbon::parse($afspraak->Datum)->format('d-m-Y') }}</td>
                                    {{-- Starttijd en de in de stored procedure berekende eindtijd --}}
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ substr($afspraak->Starttijd, 0, 5) }} - {{ substr($afspraak->Eindtijd, 0, 5) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $afspraak->Status }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="inline-flex items-center gap-2">
                                            @if (Route::has('afspraken.edit'))
                                                <a href="{{ route('afspraken.edit', $afspraak->Id) }}"
                                                   class="inline-flex items-center justify-center rounded-md bg-amber-500 p-1.5 text-white hover:bg-amber-400"
                                                   title="Wijzigen" aria-label="Wijzigen">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if (Route::has('afspraken.destroy'))
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center justify-center rounded-md bg-red-600 p-1.5 text-white hover:bg-red-500"
                                                    title="Verwijderen" aria-label="Verwijderen"
                                                    data-id="{{ $afspraak->Id }}"
                                                    data-delete-url="{{ route('afspraken.destroy', $afspraak->Id) }}"
                                                    data-klant="{{ $afspraak->KlantNaam }}"
                                                    data-medewerker="{{ $afspraak->MedewerkerNaam }}"
                                                    data-behandeling="{{ $afspraak->BehandelingNaam }}"
                                                    data-datumtijd="{{ \Illuminate\Support\Carbon::parse($afspraak->Datum)->format('d-m-Y') }} om {{ substr($afspraak->Starttijd, 0, 5) }}"
                                                    onclick="openDeleteModal(this)"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-300">
                                        Geen afspraken gevonden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal voor verwijderbevestiging met verplichte code "VERWIJDEREN"; de server controleert dit ook. --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="text-lg font-semibold text-gray-900">Afspraak verwijderen</h3>
            <p class="mt-2 text-sm text-gray-700">Weet je zeker dat je deze afspraak wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.</p>

            <dl class="mt-3 space-y-1 rounded-md border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                <div><dt class="inline font-semibold">Klant:</dt> <dd id="modalKlant" class="inline"></dd></div>
                <div><dt class="inline font-semibold">Medewerker:</dt> <dd id="modalMedewerker" class="inline"></dd></div>
                <div><dt class="inline font-semibold">Behandeling:</dt> <dd id="modalBehandeling" class="inline"></dd></div>
                <div><dt class="inline font-semibold">Datum:</dt> <dd id="modalDatumTijd" class="inline"></dd></div>
            </dl>

            <p class="mt-3 text-sm text-gray-700">Typ exact <span class="font-bold">VERWIJDEREN</span> om te bevestigen.</p>

            <form id="deleteForm" method="POST" class="mt-4 space-y-3" novalidate>
                @csrf
                @method('DELETE')
                <div id="deleteModalError" class="hidden rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></div>
                <input
                    type="text"
                    name="Bevestiging"
                    id="Bevestiging"
                    autocomplete="off"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="VERWIJDEREN"
                >
                <div class="flex items-center justify-center gap-2">
                    <button type="button" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300" onclick="closeDeleteModal()">
                        Annuleren
                    </button>
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                        Verwijderen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Opent de modal en koppelt de juiste delete-url en gegevens aan het formulier.
        function openDeleteModal(button) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const codeInput = document.getElementById('Bevestiging');
            const errorBox = document.getElementById('deleteModalError');

            form.action = button.getAttribute('data-delete-url');
            document.getElementById('modalKlant').textContent = button.getAttribute('data-klant');
            document.getElementById('modalMedewerker').textContent = button.getAttribute('data-medewerker');
            document.getElementById('modalBehandeling').textContent = button.getAttribute('data-behandeling');
            document.getElementById('modalDatumTijd').textContent = button.getAttribute('data-datumtijd');

            codeInput.value = '';
            codeInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            errorBox.textContent = '';
            errorBox.classList.add('hidden');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Sluit de modal en reset de visuele status.
        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Vervangt browser-validatie met een custom melding binnen de modal.
        document.getElementById('deleteForm').addEventListener('submit', function (event) {
            const codeInput = document.getElementById('Bevestiging');
            const errorBox = document.getElementById('deleteModalError');
            const ingevoerd = (codeInput.value || '').trim();

            if (ingevoerd !== 'VERWIJDEREN') {
                event.preventDefault();

                errorBox.textContent = 'Je moet exact VERWIJDEREN invoeren om te kunnen verwijderen.';
                errorBox.classList.remove('hidden');
                codeInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                codeInput.focus();
            }
        });

        // Laat de succesmelding automatisch na 4 seconden vloeiend verdwijnen.
        const successFlashMessage = document.getElementById('successFlashMessage');
        if (successFlashMessage) {
            setTimeout(function () {
                successFlashMessage.style.opacity = '0';
                setTimeout(function () {
                    successFlashMessage.remove();
                }, 500);
            }, 4000);
        }

        @if (request()->filled('verwijder'))
            // De server stuurt hier bij een mislukte bevestiging naartoe terug: heropen dezelfde modal.
            window.addEventListener('DOMContentLoaded', function () {
                const knop = document.querySelector('[data-id="{{ request('verwijder') }}"]');
                if (knop) {
                    openDeleteModal(knop);

                    @if ($errors->has('Bevestiging'))
                        document.getElementById('Bevestiging').value = @json(old('Bevestiging', ''));

                        const errorBox = document.getElementById('deleteModalError');
                        errorBox.textContent = @json($errors->first('Bevestiging'));
                        errorBox.classList.remove('hidden');
                        document.getElementById('Bevestiging').classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    @endif
                }
            });
        @endif
    </script>
</x-app-layout>
