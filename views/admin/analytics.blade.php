@extends('admin.layout')

@section('content')
    <div class="analytics-page">
        <div class="page-header">
            <h2>Analytics Dashboard</h2>
            <p>View detailed analytics for your shortened links</p>
        </div>

        <div class="analytics-stats">
            <div class="stat-card">
                <h3>Total Links</h3>
                <div class="stat-number">{{ $totalLinks ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Visits</h3>
                <div class="stat-number">{{ $totalVisits ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <h3>Today's Visits</h3>
                <div class="stat-number">{{ $todayVisits ?? 0 }}</div>
            </div>
        </div>

        <div class="analytics-sections">
            <div class="analytics-section">
                <h3>Recent Activity</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Link</th>
                                <th>Visits</th>
                                <th>Last Visit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($recentLinks) && is_array($recentLinks))
                                @foreach ($recentLinks as $link)
                                    <tr>
                                        <td>
                                            <a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $link['code'] }}"
                                                target="_blank" class="url-link">
                                                {{ $link['code'] }}
                                            </a>
                                        </td>
                                        <td>{{ $link['visit_count'] }}</td>
                                        <td>{{ $link['last_visit'] ?? 'Never' }}</td>
                                        <td>
                                            <a href="/admin/analytics?code={{ urlencode($link['code']) }}"
                                                class="btn btn-small">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No recent activity</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
