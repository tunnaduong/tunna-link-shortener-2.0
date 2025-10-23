@extends('admin.layout')

@section('content')
    <div class="dashboard">
        <h2>Dashboard Overview</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Visits</h3>
                <div class="stat-number">{{ $stats['totalVisits'] ?? 0 }}</div>
                <div class="stat-trend {{ $stats['totalVisitsTrend']['class'] ?? 'neutral' }}">
                    <span class="trend-icon">
                        @if (($stats['totalVisitsTrend']['direction'] ?? 'neutral') == 'up')
                            ↗
                        @elseif(($stats['totalVisitsTrend']['direction'] ?? 'neutral') == 'down')
                            ↘
                        @else
                            →
                        @endif
                    </span>
                    <span class="trend-text">{{ $stats['totalVisitsTrend']['percentage'] ?? 0 }}%</span>
                </div>
            </div>

            <div class="stat-card">
                <h3>Visits Today</h3>
                <div class="stat-number">{{ $stats['visitsToday'] ?? 0 }}</div>
                <div class="stat-trend {{ $stats['visitsTrend']['class'] ?? 'neutral' }}">
                    <span class="trend-icon">
                        @if (($stats['visitsTrend']['direction'] ?? 'neutral') == 'up')
                            ↗
                        @elseif(($stats['visitsTrend']['direction'] ?? 'neutral') == 'down')
                            ↘
                        @else
                            →
                        @endif
                    </span>
                    <span class="trend-text">{{ $stats['visitsTrend']['percentage'] ?? 0 }}%</span>
                </div>
            </div>

            <div class="stat-card">
                <h3>Redirects Completed</h3>
                <div class="stat-number">{{ $stats['redirectsCompleted'] ?? 0 }}</div>
                <div class="stat-subtitle">{{ $stats['redirectsCompletedToday'] ?? 0 }} today</div>
                <div class="stat-trend {{ $stats['redirectsTrend']['class'] ?? 'neutral' }}">
                    <span class="trend-icon">
                        @if (($stats['redirectsTrend']['direction'] ?? 'neutral') == 'up')
                            ↗
                        @elseif(($stats['redirectsTrend']['direction'] ?? 'neutral') == 'down')
                            ↘
                        @else
                            →
                        @endif
                    </span>
                    <span class="trend-text">{{ $stats['redirectsTrend']['percentage'] ?? 0 }}%</span>
                </div>
            </div>

            <div class="stat-card">
                <h3>Completion Rate Today</h3>
                <div class="stat-number">{{ $stats['redirectsCompletedToday'] ?? 0 }}/{{ $stats['visitsToday'] ?? 0 }}
                </div>
                <div class="stat-percentage">{{ $stats['completionPercentageToday'] ?? 0 }}%</div>
                <div class="stat-trend {{ $stats['completionTrend']['class'] ?? 'neutral' }}">
                    <span class="trend-icon">
                        @if (($stats['completionTrend']['direction'] ?? 'neutral') == 'up')
                            ↗
                        @elseif(($stats['completionTrend']['direction'] ?? 'neutral') == 'down')
                            ↘
                        @else
                            →
                        @endif
                    </span>
                    <span class="trend-text">{{ $stats['completionTrend']['percentage'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="dashboard-section">
                <h3>Activity - Last 30 Days</h3>
                <div class="chart-container" style="position: relative; height: 320px;">
                    <canvas id="visitsChart" height="320"></canvas>
                </div>
            </div>

            <div class="dashboard-section">
                <h3>Recent Links</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>URL</th>
                                <th>Title</th>
                                <th>Visits</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($recentLinks) && is_array($recentLinks))
                                @foreach ($recentLinks as $link)
                                    <tr>
                                        <td><code><a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $link['code'] }}"
                                                    target="_blank" class="url-link">{{ $link['code'] }}</a></code></td>
                                        <td class="url-cell">
                                            <a href="{{ $link['next_url'] }}" target="_blank" class="url-link">
                                                {{ strlen($link['next_url']) > 50 ? substr($link['next_url'], 0, 50) . '...' : $link['next_url'] }}
                                            </a>
                                        </td>
                                        <td>{{ $link['link_title'] ?? 'No title' }}</td>
                                        <td>{{ $link['visit_count'] }}</td>
                                        <td>{{ date('M j, Y', strtotime($link['created_at'])) }}</td>
                                        <td>
                                            <a href="/admin/analytics?code={{ urlencode($link['code']) }}"
                                                class="btn btn-small">Analytics</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">No recent links found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-section">
                <h3>Recent Visits</h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Link Code</th>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Browser</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($recentVisits) && is_array($recentVisits))
                                @foreach ($recentVisits as $visit)
                                    <tr>
                                        <td><a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $visit['ref_code'] }}"
                                                target="_blank" class="url-link"><code>{{ $visit['ref_code'] }}</code></a>
                                        </td>
                                        <td>{{ $visit['ip_address'] }}</td>
                                        <td>{{ $visit['location'] ?? 'Unknown' }}</td>
                                        <td>{{ $visit['browser'] ?? 'Unknown' }}</td>
                                        <td>{{ date('M j, Y H:i', strtotime($visit['time_of_visit'])) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">No recent visits found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const series = {!! json_encode($dailySeries ?? ['labels' => [], 'visits' => [], 'completed' => []]) !!};
            const ctx = document.getElementById('visitsChart');
            if (!ctx || !series || !Array.isArray(series.labels)) return;

            const data = {
                labels: series.labels,
                datasets: [
                    {
                        label: 'Visits',
                        data: series.visits,
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Completed Redirects',
                        data: series.completed,
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    }
                ]
            };

            new Chart(ctx, {
                type: 'line',
                data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        })();
    </script>
@endsection
