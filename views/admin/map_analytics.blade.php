@extends('admin.layout')

@section('content')
<div class="analytics-page">
  <div class="page-header">
    <h2>User Location Map</h2>
    <p class="page-description">View geographical locations of users accessing shortened links</p>
  </div>

  <div class="map-analytics-container">
    <div class="map-controls">
      <div class="control-group">
        <label for="map-filter">Filter by visit count:</label>
        <select id="map-filter">
          <option value="all">All</option>
          <option value="1-5">1-5 visits</option>
          <option value="6-20">6-20 visits</option>
          <option value="21-50">21-50 visits</option>
          <option value="50+">50+ visits</option>
        </select>
      </div>
      <div class="control-group">
        <button id="refresh-map" class="btn btn-primary">Refresh Map</button>
      </div>
    </div>

    <div class="map-stats">
      <div class="stat-card">
        <h3>Total Locations</h3>
        <span class="stat-number" id="total-locations">{{ count($locationData) }}</span>
      </div>
      <div class="stat-card">
        <h3>Total Visits</h3>
        <span class="stat-number" id="total-visits">{{ array_sum(array_column($locationData, 'visit_count')) }}</span>
      </div>
      <div class="stat-card">
        <h3>Most Popular Location</h3>
        <span class="stat-number" id="top-location">{{ !empty($locationData) ? $locationData[0]['location'] : 'N/A'
          }}</span>
      </div>
    </div>

    <div class="map-container">
      <div id="map" style="height: 600px; width: 100%; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
      </div>
    </div>

    <div class="location-list">
      <h3>Location List</h3>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Location</th>
              <th>Visit Count</th>
              <th>First Visit</th>
              <th>Last Visit</th>
              <th>IP Addresses</th>
            </tr>
          </thead>
          <tbody>
            @foreach($locationData as $location)
            <tr class="location-row" data-visits="{{ $location['visit_count'] }}">
              <td>{{ $location['location'] }}</td>
              <td><span class="visit-count">{{ $location['visit_count'] }}</span></td>
              <td>{{ date('d/m/Y H:i', strtotime($location['first_visit'])) }}</td>
              <td>{{ date('d/m/Y H:i', strtotime($location['last_visit'])) }}</td>
              <td>
                <div class="ip-list">
                  @foreach(array_slice($location['ip_addresses'], 0, 3) as $ip)
                  <span class="ip-tag">{{ $ip }}</span>
                  @endforeach
                  @if(count($location['ip_addresses']) > 3)
                  <span class="ip-more">+{{ count($location['ip_addresses']) - 3 }} more</span>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
  .map-analytics-container {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .page-header {
    margin-bottom: 30px;
    text-align: center;
  }

  .page-header h2 {
    color: #333;
    margin-bottom: 10px;
  }

  .page-description {
    color: #666;
    font-size: 16px;
  }

  .map-controls {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    align-items: center;
  }

  .control-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .control-group label {
    font-weight: 500;
    color: #333;
  }

  .control-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
  }

  .map-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    opacity: 0.9;
  }

  .stat-number {
    font-size: 24px;
    font-weight: bold;
  }

  .map-container {
    margin-bottom: 30px;
  }

  .location-list h3 {
    margin-bottom: 15px;
    color: #333;
  }

  .ip-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }

  .ip-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 12px;
  }

  .ip-more {
    color: #666;
    font-size: 12px;
    font-style: italic;
  }

  .location-row:hover {
    background-color: #f5f5f5;
  }

  .visit-count {
    font-weight: bold;
    color: #1976d2;
  }

  @media (max-width: 768px) {
    .map-controls {
      flex-direction: column;
      align-items: stretch;
    }

    .map-stats {
      grid-template-columns: 1fr;
    }

    #map {
      height: 400px;
    }
  }
</style>

<script>
  // Initialize map when page loads
  document.addEventListener("DOMContentLoaded", function () {
    initializeMap();
    setupEventListeners();
  });

  function initializeMap() {
    // Initialize Leaflet map
    const map = L.map("map").setView([20.0, 105.0], 6); // Center on Vietnam

    // Add OpenStreetMap tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "© OpenStreetMap contributors"
    }).addTo(map);

    // Store map reference globally
    window.analyticsMap = map;

    // Add markers for each location
    addLocationMarkers();
  }

  function addLocationMarkers() {
    const locationData = {!! json_encode($locationData)!!
  };

  locationData.forEach(function (location) {
    if (location.coordinates && location.coordinates.lat && location.coordinates.lng) {
      // Create custom marker icon based on visit count
      const markerColor = getMarkerColor(location.visit_count);

      const marker = L.circleMarker([location.coordinates.lat, location.coordinates.lng], {
        radius: Math.min(Math.max(location.visit_count * 2, 8), 30),
        fillColor: markerColor,
        color: "#fff",
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8
      });

      // Add popup with location info
      const popupContent = `
                <div style="min-width: 200px;">
                    <h4 style="margin: 0 0 10px 0; color: #333;">${location.location}</h4>
                    <p style="margin: 5px 0;"><strong>Visit Count:</strong> ${location.visit_count}</p>
                    <p style="margin: 5px 0;"><strong>First Visit:</strong> ${new Date(location.first_visit).toLocaleDateString()}</p>
                    <p style="margin: 5px 0;"><strong>Last Visit:</strong> ${new Date(location.last_visit).toLocaleDateString()}</p>
                    <p style="margin: 5px 0;"><strong>IP Addresses:</strong> ${location.ip_addresses.length}</p>
                </div>
            `;

      marker.bindPopup(popupContent);
      marker.addTo(window.analyticsMap);
    }
  });
}

  function getMarkerColor(visitCount) {
    if (visitCount >= 50) return "#d32f2f"; // Red
    if (visitCount >= 20) return "#f57c00"; // Orange
    if (visitCount >= 10) return "#fbc02d"; // Yellow
    if (visitCount >= 5) return "#388e3c";  // Green
    return "#1976d2"; // Blue
  }

  function setupEventListeners() {
    // Filter functionality
    const filterSelect = document.getElementById("map-filter");
    filterSelect.addEventListener("change", function () {
      filterLocations(this.value);
    });

    // Refresh button
    const refreshBtn = document.getElementById("refresh-map");
    refreshBtn.addEventListener("click", function () {
      location.reload();
    });
  }

  function filterLocations(filterValue) {
    const rows = document.querySelectorAll(".location-row");

    rows.forEach(function (row) {
      const visits = parseInt(row.dataset.visits);
      let show = true;

      switch (filterValue) {
        case "1-5":
          show = visits >= 1 && visits <= 5;
          break;
        case "6-20":
          show = visits >= 6 && visits <= 20;
          break;
        case "21-50":
          show = visits >= 21 && visits <= 50;
          break;
        case "50+":
          show = visits >= 50;
          break;
        case "all":
        default:
          show = true;
          break;
      }

      row.style.display = show ? "" : "none";
    });
  }
</script>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection