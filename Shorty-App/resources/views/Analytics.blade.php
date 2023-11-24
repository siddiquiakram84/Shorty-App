<!-- resources/views/analytics.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">Analytics for Shortened URL</h1>
    </x-slot>

    <div class="container mx-auto mt-4">
        <div class="bg-white p-4 rounded-md shadow-md">
            
            @if ($url)
                <div class="text-xl font-bold mb-4">
                    Analytics for: {{ $url->original_url }}
                </div>
            @else
                <div class="text-xl font-bold text-red-500">
                    URL not found
                </div>
            @endif

            <div class="mt-4">
                @if ($url && $url->analytics->isEmpty())
                    <p>No analytics data available yet.</p>
                @elseif ($url)
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border-collapse border">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-4 py-2">Index</th>
                                    <th class="border px-4 py-2">User Agent</th>
                                    <th class="border px-4 py-2">IP Address</th>
                                    <th class="border px-4 py-2">OS</th>
                                    <th class="border px-4 py-2">Device</th>
                                    <th class="border px-4 py-2">Browser</th>
                                    <th class="border px-4 py-2">User Location</th>
                                    <th class="border px-4 py-2">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($url->analytics as $index => $analytics)
                                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-100' : '' }}">
                                        <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->user_agent }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->ip_address }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->os }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->device }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->browser }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->user_location }}</td>
                                        <td class="border px-4 py-2">{{ $analytics->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
    
    <script>
        function sendLocationData(urlId) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;

                    // Send the location data to the server
                    sendLocationAjax(urlId, latitude, longitude);
                });
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
        }

        function sendLocationAjax(urlId, latitude, longitude) {
            // Use AJAX to send location data to the server
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "{{ route('save-location') }}", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log("Location data sent successfully");
                    } else {
                        console.error("Failed to send location data");
                    }
                }
            };

            var data = JSON.stringify({
                urlId: urlId,
                latitude: latitude,
                longitude: longitude,
            });

            xhr.send(data);
        }
    </script>

    
</x-app-layout>
