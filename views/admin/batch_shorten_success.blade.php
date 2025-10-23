@extends('admin.layout')

@section('content')
    <div class="batch-shorten-success-page">
        <div class="success-header">
            <h2>✅ Batch Shorten Completed!</h2>
            <p>{{ count($links) }} links have been created successfully.</p>
        </div>

        <div class="links-list">
            <h3>Created Links</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>URL</th>
                            <th>Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($links as $link)
                            <tr>
                                <td>
                                    <a href="{{ $_ENV['APP_URL'] ?? 'https://tunn.ad' }}/{{ $link->getCode() }}"
                                        target="_blank" class="url-link">
                                        {{ $link->getCode() }}
                                    </a>
                                </td>
                                <td class="url-cell">
                                    <a href="{{ $link->getNextUrl() }}" target="_blank" class="url-link">
                                        {{ strlen($link->getNextUrl()) > 50 ? substr($link->getNextUrl(), 0, 50) . '...' : $link->getNextUrl() }}
                                    </a>
                                </td>
                                <td>{{ $link->getLinkTitle() ?? 'No title' }}</td>
                                <td>
                                    <a href="/admin/analytics?code={{ urlencode($link->getCode()) }}"
                                        class="btn btn-small">Analytics</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="success-actions">
            <a href="/admin/create-link" class="btn">Create More Links</a>
            <a href="/admin/links" class="btn btn-secondary">Manage All Links</a>
        </div>
    </div>
@endsection
