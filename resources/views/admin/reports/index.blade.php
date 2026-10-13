@extends('layouts/layoutMaster')

@section('title', 'Reports & Analytics')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js'
])
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Reports & Analytics</h4>
    <p class="text-muted mb-0">Platform performance overview</p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <select class="form-select form-select-sm" id="period-select" style="width:auto;">
      <option value="7">Last 7 Days</option>
      <option value="30" selected>Last 30 Days</option>
      <option value="90">Last 90 Days</option>
      <option value="365">This Year</option>
    </select>
    <button class="btn btn-outline-primary btn-sm">
      <i class="ti ti-download me-1"></i>Export
    </button>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row g-4 mb-4">
  @php
    $kpis = [
      ['label' => 'Total Revenue',   'value' => '$' . number_format($stats['total_revenue'] ?? 0, 2), 'icon' => 'ti-currency-dollar', 'color' => 'primary',   'change' => '+23.5%', 'up' => true],
      ['label' => 'Total Orders',    'value' => number_format($stats['total_orders'] ?? 0),           'icon' => 'ti-shopping-bag',     'color' => 'success',   'change' => '+8.2%',  'up' => true],
      ['label' => 'Active Vendors',  'value' => number_format($stats['active_vendors'] ?? 0),         'icon' => 'ti-store',            'color' => 'warning',   'change' => '+4.1%',  'up' => true],
      ['label' => 'Avg. Order Value','value' => '$' . number_format($stats['avg_order'] ?? 0, 2),    'icon' => 'ti-chart-bar',        'color' => 'info',      'change' => '-1.2%',  'up' => false],
    ];
  @endphp
  @foreach($kpis as $kpi)
  <div class="col-sm-6 col-xl-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="avatar bg-label-{{ $kpi['color'] }} rounded">
            <i class="ti {{ $kpi['icon'] }} text-{{ $kpi['color'] }}"></i>
          </div>
          <span class="badge bg-label-{{ $kpi['up'] ? 'success' : 'danger' }}">
            <i class="ti ti-trending-{{ $kpi['up'] ? 'up' : 'down' }} me-1"></i>
            {{ $kpi['change'] }}
          </span>
        </div>
        <h4 class="fw-bold mb-0">{{ $kpi['value'] }}</h4>
        <div class="text-muted small">{{ $kpi['label'] }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- Charts Row --}}
<div class="row g-4 mb-4">

  {{-- Revenue Chart --}}
  <div class="col-xl-8">
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0">Revenue Overview</h6>
        <div class="d-flex gap-3 small text-muted">
          <span><span class="badge bg-primary me-1">&nbsp;</span>Revenue</span>
          <span><span class="badge bg-success me-1">&nbsp;</span>Orders</span>
        </div>
      </div>
      <div class="card-body">
        <div id="revenue-chart" style="min-height:280px;"></div>
      </div>
    </div>
  </div>

  {{-- Top Categories --}}
  <div class="col-xl-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Sales by Category</h6>
      </div>
      <div class="card-body">
        <div id="category-chart" style="min-height:200px;"></div>
        <div class="mt-3">
          @forelse($catList ?? [] as $cat)
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
              <div style="width:10px;height:10px;border-radius:2px;background:var(--bs-{{ $cat['color'] }})"></div>
              <small>{{ $cat['name'] }}</small>
            </div>
            <small class="fw-semibold">{{ $cat['pct'] }}%</small>
          </div>
          @empty
          <p class="text-muted small mb-0">No categories found.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Top Vendors + Top Products --}}
<div class="row g-4">

  {{-- Top Vendors --}}
  <div class="col-xl-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Top Performing Vendors</h6>
      </div>
      <div class="card-body p-0">
        @forelse($topVendors ?? [] as $vendor)
        @php $vi = ($loop->index % 14) + 1; $rank = $loop->index + 1; @endphp
        <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
          <span class="fw-bold text-muted" style="width:24px;">{{ $rank }}.</span>
          <img src="{{ asset('assets/img/avatars/' . $vi . '.png') }}"
               onerror="this.src='{{ asset('assets/img/avatars/1.png') }}'"
               alt="{{ $vendor->name }}"
               style="width:40px;height:40px;border-radius:50%;object-fit:cover;" />
          <div class="flex-grow-1">
            <div class="fw-semibold small">{{ $vendor->name }}</div>
            <div class="progress mt-1" style="height:4px;width:150px;">
              <div class="progress-bar bg-primary" style="width:{{ min(100, $vendor->sales_percent ?? 50) }}%"></div>
            </div>
          </div>
          <div class="text-end">
            <div class="fw-bold small text-primary">${{ number_format($vendor->total_revenue ?? 0, 2) }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $vendor->orders_count ?? 0 }} orders</div>
          </div>
        </div>
        @empty
        <div class="p-3 text-muted small">No active vendors found.</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Recent Activity --}}
  <div class="col-xl-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header border-0">
        <h6 class="fw-bold mb-0">Recent Activity</h6>
      </div>
      <div class="card-body">
        @forelse($activities ?? [] as $activity)
        <div class="d-flex align-items-start gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
          <div class="avatar avatar-sm bg-label-{{ $activity['color'] }} rounded-circle flex-shrink-0">
            <i class="ti {{ $activity['icon'] }} text-{{ $activity['color'] }} ti-sm"></i>
          </div>
          <div class="flex-grow-1">
            <div class="small">{{ $activity['text'] }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $activity['time'] }}</div>
          </div>
        </div>
        @empty
        <p class="text-muted small mb-0">No recent activity recorded.</p>
        @endforelse
      </div>
    </div>
  </div>

</div>

@endsection

@section('page-script')
<script>
function renderAdminReportsCharts() {
  if (typeof ApexCharts === 'undefined') {
    return setTimeout(renderAdminReportsCharts, 100);
  }

  const revenueEl = document.getElementById('revenue-chart');
  if (revenueEl) {
    const revenueMonths = {!! json_encode($months ?? []) !!};
    const revenueValues = {!! json_encode($revenueData ?? []) !!};
    const ordersValues = {!! json_encode($ordersData ?? []) !!};

    const revenueChart = new ApexCharts(revenueEl, {
      chart: { type: 'area', height: 280, toolbar: { show: false }, sparkline: { enabled: false } },
      series: [
        { name: 'Revenue ($)', data: revenueValues },
        { name: 'Orders',      data: ordersValues }
      ],
      xaxis: {
        categories: revenueMonths,
        axisBorder: { show: false }, axisTicks: { show: false }
      },
      colors: ['#696cff', '#71dd37'],
      fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
      stroke: { curve: 'smooth', width: 2 },
      dataLabels: { enabled: false },
      legend: { show: false },
      grid: { borderColor: '#f0f0f0' }
    });
    revenueChart.render();
  }

  const catEl = document.getElementById('category-chart');
  if (catEl) {
    let catLabels = {!! json_encode($catLabels ?? []) !!};
    let catSeries = {!! json_encode($catSeries ?? []) !!};
    if (catSeries.length === 0 || catSeries.every(v => v === 0)) {
      catLabels = ['Catalog Data'];
      catSeries = [1];
    }
    const categoryChart = new ApexCharts(catEl, {
      chart: { type: 'donut', height: 200 },
      series: catSeries,
      labels: catLabels,
      colors: ['#696cff', '#71dd37', '#ffab00', '#03c3ec', '#8592a3'],
      legend: { show: false },
      dataLabels: { enabled: false },
      plotOptions: { pie: { donut: { size: '70%' } } }
    });
    categoryChart.render();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', renderAdminReportsCharts);
} else {
  renderAdminReportsCharts();
}
</script>
@endsection
