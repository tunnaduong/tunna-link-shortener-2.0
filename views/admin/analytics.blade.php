@extends('admin.layout')

@section('content')
    <div class="analytics-page">
        <h2>Analytics Overview</h2>

        <div class="analytics-sections">
            <div class="analytics-section">
                <h3>Visits by Day (Last 30 Days)</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($overallStats['visitsByDay']) && is_array($overallStats['visitsByDay']))
                                @foreach ($overallStats['visitsByDay'] as $day)
                                    <tr>
                                        <td>{{ date('M j, Y', strtotime($day['date'])) }}</td>
                                        <td>{{ $day['visits'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Top Browsers</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Browser</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($overallStats['topBrowsers']) && is_array($overallStats['topBrowsers']))
                                @foreach ($overallStats['topBrowsers'] as $browser)
                                    <tr>
                                        <td>{{ $browser['browser'] }}</td>
                                        <td>{{ $browser['count'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Top Locations</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($overallStats['topLocations']) && is_array($overallStats['topLocations']))
                                @foreach ($overallStats['topLocations'] as $location)
                                    <tr>
                                        <td>{{ $location['location'] }}</td>
                                        <td>{{ $location['count'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Top Referrers</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Referrer URL</th>
                                <th>Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($overallStats['topReferrers']) && is_array($overallStats['topReferrers']))
                                @foreach ($overallStats['topReferrers'] as $referrer)
                                    <tr>
                                        <td class="url-cell">
                                            <a href="{{ $referrer['ref_url'] }}" target="_blank" class="url-link">
                                                {{ strlen($referrer['ref_url']) > 50 ? substr($referrer['ref_url'], 0, 50) . '...' : $referrer['ref_url'] }}
                                            </a>
                                        </td>
                                        <td>{{ $referrer['count'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="analytics-section">
                <h3>Top Links by Visits</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>URL</th>
                                <th>Visits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($topLinks) && is_array($topLinks))
                                @foreach ($topLinks as $link)
                                    <tr>
                                        <td><code><a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $link['code'] }}"
                                                    target="_blank" class="url-link">{{ $link['code'] }}</a></code></td>
                                        <td class="url-cell">
                                            <a href="{{ $link['next_url'] }}" target="_blank" class="url-link">
                                                {{ strlen($link['next_url']) > 40 ? substr($link['next_url'], 0, 40) . '...' : $link['next_url'] }}
                                            </a>
                                        </td>
                                        <td>{{ $link['visit_count'] }}</td>
                                        <td>
                                            <a href="/admin/analytics?code={{ urlencode($link['code']) }}"
                                                class="btn btn-small">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No data available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
