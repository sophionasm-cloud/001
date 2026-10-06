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
      ['label' => 'Total Revenue',   'value' => '$' . number_format($stats['total_revenue']   ?? 128450, 2), 'icon' => 'ti-currency-dollar', 'color' => 'primary',   'change' => '+23.5%', 'up' => true],
      ['label' => 'Total Orders',    'value' => number_format($stats['total_orders']    ?? 5623),            'icon' => 'ti-shopping-bag',     'color' => 'success',   'change' => '+8.2%',  'up' => true],
      ['label' => 'Active Vendors',  'value' => number_format($stats['active_vendors']  ?? 84),              'icon' => 'ti-store',            'color' => 'warning',   'change' => '+4.1%',  'up' => true],
      ['label' => 'Avg. Order Value','value' => '$' . number_format($stats['avg_order'] ?? 68.4, 2),        'icon' => 'ti-chart-bar',        'color' => 'info',      'change' => '-1.2%',  'up' => false],
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
          @php
            $cats = [
              ['name' => 'Electronics', 'pct' => 35, 'color' => 'primary'],
              ['name' => 'Fashion',     'pct' => 25, 'color' => 'success'],
              ['name' => 'Home & Garden','pct' => 20, 'color' => 'warning'],
              ['name' => 'Sports',      'pct' => 12, 'color' => 'info'],
              ['name' => 'Other',       'pct' => 8,  'color' => 'secondary'],
            ];
          @endphp
          @foreach($cats as $cat)
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
              <div style="width:10px;height:10px;border-radius:2px;background:var(--bs-{{ $cat['color'] }})"></div>
              <small>{{ $cat['name'] }}</small>
            </div>
            <small class="fw-semibold">{{ $cat['pct'] }}%</small>
          </div>
          @endforeach
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
          <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $vi . '.png') }}"
               onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
               alt="{{ $vendor->name }}"
               style="width:40px;height:40px;border-radius:50%;object-fit:cover;" />
          <div class="flex-grow-1">
            <div class="fw-semibold small">{{ $vendor->name }}</div>
            <div class="progress mt-1" style="height:4px;width:150px;">
              <div class="progress-bar bg-primary" style="width:{{ min(100, $vendor->sales_percent ?? rand(40,90)) }}%"></div>
            </div>
          </div>
          <div class="text-end">
            <div class="fw-bold small text-primary">${{ number_format($vendor->total_revenue ?? 0) }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $vendor->orders_count ?? 0 }} orders</div>
          </div>
        </div>
        @empty
        {{-- Demo data when no vendors --}}
        @php
          $demoVendors = [
            ['name'=>'TechStore Pro','revenue'=>42300,'orders'=>312,'ai'=>1],
            ['name'=>'FashionHub','revenue'=>31800,'orders'=>248,'ai'=>2],
            ['name'=>'HomeDecor Plus','revenue'=>28900,'orders'=>201,'ai'=>3],
            ['name'=>'SportsWorld','revenue'=>19500,'orders'=>176,'ai'=>4],
            ['name'=>'GadgetZone','revenue'=>15200,'orders'=>143,'ai'=>5],
          ];
        @endphp
        @foreach($demoVendors as $idx => $dv)
        <div class="d-flex align-items-center gap-3 p-3 {{ $idx < count($demoVendors)-1 ? 'border-bottom' : '' }}">
          <span class="fw-bold text-muted" style="width:24px;">{{ $idx+1 }}.</span>
          <img src="{{ Vite::asset('resources/images/avatars/avatar-' . $dv['ai'] . '.png') }}"
               onerror="this.src='{{ Vite::asset('resources/images/misc/misc-1.png') }}'"
               alt="{{ $dv['name'] }}"
               style="width:40px;height:40px;border-radius:50%;object-fit:cover;" />
          <div class="flex-grow-1">
            <div class="fw-semibold small">{{ $dv['name'] }}</div>
            <div class="progress mt-1" style="height:4px;width:150px;">
              <div class="progress-bar bg-primary" style="width:{{ round($dv['revenue']/423) }}%"></div>
            </div>
          </div>
          <div class="text-end">
            <div class="fw-bold small text-primary">${{ number_format($dv['revenue']) }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $dv['orders'] }} orders</div>
          </div>
        </div>
        @endforeach
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
        @php
          $activities = [
            ['icon' => 'ti-shopping-bag', 'color' => 'success', 'text' => 'New order #5632 placed by John Doe', 'time' => '2 min ago'],
            ['icon' => 'ti-user-plus',    'color' => 'primary', 'text' => 'New vendor "TechMart" registered',  'time' => '15 min ago'],
            ['icon' => 'ti-credit-card',  'color' => 'info',    'text' => 'Payment of $248 received',          'time' => '1 hr ago'],
            ['icon' => 'ti-star',         'color' => 'warning', 'text' => 'New 5-star review on iPhone 14',   'time' => '3 hrs ago'],
            ['icon' => 'ti-alert-circle', 'color' => 'danger',  'text' => 'Low stock alert: Running Shoes',   'time' => '5 hrs ago'],
            ['icon' => 'ti-check',        'color' => 'success', 'text' => 'Vendor "FashionHub" approved',     'time' => '1 day ago'],
          ];
        @endphp
        @foreach($activities as $activity)
        <div class="d-flex align-items-start gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
          <div class="avatar avatar-sm bg-label-{{ $activity['color'] }} rounded-circle flex-shrink-0">
            <i class="ti {{ $activity['icon'] }} text-{{ $activity['color'] }} ti-sm"></i>
          </div>
          <div class="flex-grow-1">
            <div class="small">{{ $activity['text'] }}</div>
            <div class="text-muted" style="font-size:.7rem">{{ $activity['time'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>

@endsection

@section('page-script')
<script>
// Revenue Chart
const revenueChart = new ApexCharts(document.getElementById('revenue-chart'), {
  chart: { type: 'area', height: 280, toolbar: { show: false }, sparkline: { enabled: false } },
  series: [
    { name: 'Revenue ($)', data: [12000, 18000, 14000, 22000, 19000, 28000, 24000, 32000, 29000, 38000, 35000, 42000] },
    { name: 'Orders',      data: [180, 240, 200, 310, 270, 380, 340, 450, 410, 520, 480, 580] }
  ],
  xaxis: {
    categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
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

// Category Chart
const categoryChart = new ApexCharts(document.getElementById('category-chart'), {
  chart: { type: 'donut', height: 200 },
  series: [35, 25, 20, 12, 8],
  labels: ['Electronics', 'Fashion', 'Home & Garden', 'Sports', 'Other'],
  colors: ['#696cff', '#71dd37', '#ffab00', '#03c3ec', '#8592a3'],
  legend: { show: false },
  dataLabels: { enabled: false },
  plotOptions: { pie: { donut: { size: '70%' } } }
});
categoryChart.render();
</script>
@endsection
