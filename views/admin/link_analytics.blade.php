@extends('admin.layout')

@section('content')
    <div class="link-analytics-page">
        <div class="page-header">
            <h2>Link Analytics: {{ $link->getCode() }}</h2>
            <a href="/admin/analytics" class="btn">Back to Analytics</a>
        </div>

        <div class="link-info">
            <h3>Link Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Code:</strong>
                    <code>
                        <a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $link->getCode() }}" class="url-link"
                            target="_blank">
                            {{ $link->getCode() }}
                        </a>
                    </code>
                </div>
                <div class="info-item">
                    <strong>URL:</strong>
                    <a href="{{ $link->getNextUrl() }}" target="_blank" class="url-link">
                        {{ $link->getNextUrl() }}
                    </a>
                </div>
                <div class="info-item">
                    <strong>Title:</strong> {{ $link->getLinkTitle() ?? 'No title' }}
                </div>
                <div class="info-item">
                    <strong>Created:</strong> {{ $link->getCreatedAt()->format('M j, Y H:i') }}
                </div>
            </div>
        </div>

        <div class="analytics-stats">
            <div class="stat-card">
                <h3>Total Visits</h3>
                <div class="stat-number">{{ $visitStats['totalVisits'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Completed Redirects</h3>
                <div class="stat-number">{{ $visitStats['completedRedirects'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Completion Rate</h3>
                <div class="stat-number">{{ $visitStats['completionRate'] }}%</div>
            </div>
        </div>

        <div class="analytics-section">
            <h3>Location Map</h3>
            <div class="map-container">
                <div id="visitMap" style="height: 400px; width: 100%; border-radius: 8px;"></div>
            </div>
        </div>

        <div class="analytics-sections">
            <div class="analytics-section">
                <h3>Visits by Browser</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Browser</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($visitStats['visitsByBrowser'] as $browser)
                                <tr>
                                    <td>{{ $browser['browser'] }}</td>
                                    <td>{{ $browser['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Visits by Location</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visitStats['visitsByLocation'] as $location)
                                <tr>
                                    <td>{{ $location['location'] }}</td>
                                    <td>{{ $location['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Visits by Referrer</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Referrer URL</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visitStats['visitsByReferrer'] as $referrer)
                                <tr>
                                    <td class="url-cell">
                                        <a href="{{ $referrer['ref_url'] }}" target="_blank" class="url-link">
                                            {{ strlen($referrer['ref_url']) > 50 ? substr($referrer['ref_url'], 0, 50) . '...' : $referrer['ref_url'] }}
                                        </a>
                                    </td>
                                    <td>{{ $referrer['count'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Visits by ISP</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ISP</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($visitStats['visitsByIsp']))
                                @foreach ($visitStats['visitsByIsp'] as $isp)
                                    <tr>
                                        <td>{{ $isp['isp'] ?? 'Unknown' }}</td>
                                        <td>{{ $isp['count'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No ISP data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Recent Visits</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>ISP</th>
                                <th>Browser</th>
                                <th>OS</th>
                                <th>Screen Size</th>
                                <th>Referrer</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visits as $visit)
                                @php
                                    $referrerUrl = $visit['ref_url'] ?? 'Unknown';
                                    $referrerDisplay = $referrerUrl;
                                    if (
                                        $referrerUrl !== 'Unknown' &&
                                        $referrerUrl !== 'Direct visit' &&
                                        $referrerUrl !== 'Page refreshed'
                                    ) {
                                        $referrerDisplay =
                                            '<a href="' .
                                            $referrerUrl .
                                            '" target="_blank" class="url-link">' .
                                            (strlen($referrerUrl) > 30
                                                ? substr($referrerUrl, 0, 30) . '...'
                                                : $referrerUrl) .
                                            '</a>';
                                    }
                                @endphp
                                @php
                                    $coordinates = null;
                                    $lat = null;
                                    $lng = null;
                                    if (isset($visit['coordinates']) && $visit['coordinates']) {
                                        $coords = json_decode($visit['coordinates'], true);
                                        if ($coords && isset($coords['lat']) && isset($coords['lng'])) {
                                            $lat = $coords['lat'];
                                            $lng = $coords['lng'];
                                            $coordinates = $lat . ', ' . $lng;
                                        } elseif (
                                            $coords &&
                                            isset($coords['latitude']) &&
                                            isset($coords['longitude'])
                                        ) {
                                            $lat = $coords['latitude'];
                                            $lng = $coords['longitude'];
                                            $coordinates = $lat . ', ' . $lng;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $visit['ip_address'] }}</td>
                                    <td>
                                        {{ $visit['location'] ?? 'Unknown' }}
                                        @if ($coordinates)
                                            <span class="coordinates" data-lat="{{ $lat }}"
                                                data-lng="{{ $lng }}"
                                                style="display: none;">{{ $coordinates }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $visit['isp'] ?? 'Unknown' }}</td>
                                    <td>{{ $visit['browser'] ?? 'Unknown' }}</td>
                                    <td>{{ $visit['OS'] ?? 'Unknown' }}</td>
                                    <td>{{ $visit['screen_size'] ?? 'Unknown' }}</td>
                                    <td>{!! $referrerDisplay !!}</td>
                                    <td>{{ date('M j, Y H:i', strtotime($visit['time_of_visit'])) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('visitMap').setView([0, 0], 2);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Get coordinates from the table
            const coordinateElements = document.querySelectorAll('.coordinates');
            const markers = [];

            coordinateElements.forEach(function(element) {
                const lat = parseFloat(element.dataset.lat);
                const lng = parseFloat(element.dataset.lng);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const marker = L.marker([lat, lng]).addTo(map);
                    marker.bindPopup(element.textContent);
                    markers.push(marker);
                }
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                // Default view if no coordinates
                map.setView([20, 0], 2);
            }
        });
    </script>
@endsection
