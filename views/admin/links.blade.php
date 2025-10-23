@extends('admin.layout')

@section('content')
    <div class="links-page">
        <div class="page-header">
            <h2>All Links</h2>
            <div class="page-info">
                Showing {{ count($links ?? []) }} of {{ $totalLinks ?? 0 }} links
            </div>
        </div>

        <div class="search-container">
            <form method="GET" action="/admin/links" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="search" value="{{ $searchQuery ?? '' }}"
                        placeholder="Search by code, URL, or title..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                    @if (isset($searchQuery) && !empty($searchQuery))
                        <a href="/admin/links" class="clear-search-btn">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>URL</th>
                        <th>Title</th>
                        <th>Visits</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($links) && is_array($links))
                        @foreach ($links as $link)
                            @php
                                $redirectType =
                                    $link['redirect_type'] == 0
                                        ? 'Direct'
                                        : ($link['redirect_type'] == 1
                                            ? 'Click'
                                            : ($link['redirect_type'] == 2
                                                ? 'Captcha'
                                                : ($link['redirect_type'] == 3
                                                    ? 'Password'
                                                    : 'Unknown')));
                            @endphp
                            <tr>
                                <td><a href="/{{ $link['code'] }}" target="_blank"
                                        class="url-link"><code>{{ $link['code'] }}</code></a></td>
                                <td class="url-cell">
                                    <a href="{{ $link['next_url'] }}" target="_blank" class="url-link">
                                        {{ strlen($link['next_url']) > 40 ? substr($link['next_url'], 0, 40) . '...' : $link['next_url'] }}
                                    </a>
                                </td>
                                <td>{{ $link['link_title'] ?? 'No title' }}</td>
                                <td>{{ $link['visit_count'] }}</td>
                                <td>{{ $redirectType }}</td>
                                <td>{{ date('M j, Y', strtotime($link['created_at'])) }}</td>
                                <td class="actions">
                                    <a href="/admin/edit-link?code={{ urlencode($link['code']) }}"
                                        class="btn btn-small">Manage</a>
                                    <a href="/admin/analytics?code={{ urlencode($link['code']) }}"
                                        class="btn btn-small">Analytics</a>
                                    <button onclick="confirmDelete('{{ $link['code'] }}')"
                                        class="btn btn-small btn-danger">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No links found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="pagination">
            @if (($totalPages ?? 1) > 1)
                <div class="pagination-info">Page {{ $currentPage ?? 1 }} of {{ $totalPages ?? 1 }}</div>

                @if (($currentPage ?? 1) > 1)
                    <a href="/admin/links?page={{ ($currentPage ?? 1) - 1 }}{{ isset($searchQuery) && !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' }}"
                        class="btn">Previous</a>
                @endif

                @if (($currentPage ?? 1) < ($totalPages ?? 1))
                    <a href="/admin/links?page={{ ($currentPage ?? 1) + 1 }}{{ isset($searchQuery) && !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' }}"
                        class="btn">Next</a>
                @endif
            @endif
        </div>
    </div>
@endsection
