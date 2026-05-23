<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visit analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Unique visits per hour</h3>
                    <canvas id="hourlyChart" height="220"></canvas>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Visits by city</h3>
                    <canvas id="cityChart" height="220"></canvas>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-6 overflow-x-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent visits</h3>
                <table class="min-w-full text-sm text-left">
                    <thead class="border-b text-gray-600">
                        <tr>
                            <th class="py-2 pr-4">Time</th>
                            <th class="py-2 pr-4">City</th>
                            <th class="py-2 pr-4">Device</th>
                            <th class="py-2 pr-4">URL</th>
                            <th class="py-2">Unique/hour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentVisits as $visit)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pr-4 whitespace-nowrap">{{ $visit->visited_at->format('d.m.Y H:i') }}</td>
                                <td class="py-2 pr-4">{{ $visit->city ?? '—' }}</td>
                                <td class="py-2 pr-4">{{ $visit->device_type }}</td>
                                <td class="py-2 pr-4 max-w-md truncate" title="{{ $visit->url }}">{{ $visit->url }}</td>
                                <td class="py-2">{{ $visit->is_unique_in_hour ? 'Yes' : 'No' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-gray-500">No visits recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            const hourlyStats = @json($hourlyStats);
            const cityStats = @json($cityStats);

            new Chart(document.getElementById('hourlyChart'), {
                type: 'line',
                data: {
                    labels: hourlyStats.map((row) => row.hour),
                    datasets: [{
                        label: 'Unique visits',
                        data: hourlyStats.map((row) => row.unique_visits),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        fill: true,
                        tension: 0.25,
                    }],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });

            new Chart(document.getElementById('cityChart'), {
                type: 'doughnut',
                data: {
                    labels: cityStats.map((row) => row.city),
                    datasets: [{
                        data: cityStats.map((row) => row.count),
                        backgroundColor: [
                            '#2563eb', '#16a34a', '#dc2626', '#ca8a04',
                            '#7c3aed', '#0891b2', '#db2777', '#4b5563',
                        ],
                    }],
                },
                options: {
                    responsive: true,
                },
            });
        </script>
    @endpush
</x-app-layout>
