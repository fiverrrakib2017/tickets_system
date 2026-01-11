<?php
// -------------------- Metrics (safe & pretty) --------------------
date_default_timezone_set("Asia/Dhaka");

// Uptime (from /proc/uptime; fallback to `uptime -p`)
$uptimeSeconds = (int)trim(@shell_exec('cut -d. -f1 /proc/uptime 2>/dev/null'));
if ($uptimeSeconds > 0) {
    $d = intdiv($uptimeSeconds, 86400);
    $h = intdiv($uptimeSeconds % 86400, 3600);
    $m = intdiv(($uptimeSeconds % 3600), 60);
    $uptimePretty = sprintf('%d d %02d h %02d m', $d, $h, $m);
} else {
    $uptimePretty = trim(@shell_exec('uptime -p 2>/dev/null')) ?: '—';
}

// CPU usage (5-min load / cores)
$loads = function_exists('sys_getloadavg') ? sys_getloadavg() : [0,0,0];
$load5 = $loads[1] ?? 0;
$cores = (int)trim(@shell_exec('nproc 2>/dev/null'));
if ($cores <= 0) {
    $cores = (int)trim(@shell_exec("grep -c '^processor' /proc/cpuinfo 2>/dev/null")) ?: 1;
}
$cpuPercent = (int)round(($load5 / max($cores,1)) * 100);
$cpuPercent = max(0, min(100, $cpuPercent));
$cpuBarClass = $cpuPercent < 50 ? 'bg-success' : ($cpuPercent < 80 ? 'bg-warning' : 'bg-danger');

// RAM usage (MemTotal - MemAvailable)
$meminfo = @file('/proc/meminfo');
$memTotalKB = $memAvailKB = null;
if ($meminfo) {
    foreach ($meminfo as $line) {
        if (strpos($line, 'MemTotal:') === 0)      $memTotalKB  = (int)filter_var($line, FILTER_SANITIZE_NUMBER_INT);
        if (strpos($line, 'MemAvailable:') === 0)  $memAvailKB  = (int)filter_var($line, FILTER_SANITIZE_NUMBER_INT);
    }
}
if ($memTotalKB && $memAvailKB) {
    $memUsedKB   = $memTotalKB - $memAvailKB;
    $memPercent  = (int)round(($memUsedKB / $memTotalKB) * 100);
    $memUsedGB   = number_format($memUsedKB / 1048576, 2);
    $memTotalGB  = number_format($memTotalKB / 1048576, 2);
    $memText     = "{$memUsedGB} GB / {$memTotalGB} GB";
} else {
    // Fallback: `free -k`
    $free = trim(@shell_exec('free -k 2>/dev/null'));
    if ($free) {
        $lines = explode("\n", $free);
        $parts = preg_split('/\s+/', $lines[1] ?? '');
        // total used free shared buff/cache available
        $total = (int)($parts[1] ?? 0);
        $avail = (int)($parts[6] ?? 0);
        $used  = $total - $avail;
        $memPercent = $total ? (int)round($used / $total * 100) : 0;
        $memText = number_format($used/1048576,2)." GB / ".number_format($total/1048576,2)." GB";
    } else {
        $memPercent = 0; $memText = '—';
    }
}
$memBarClass = $memPercent < 60 ? 'bg-success' : ($memPercent < 85 ? 'bg-warning' : 'bg-danger');

// System time
$sysTime  = date('h:i:s A');
$sysDate  = date('D, M j, Y');

// DB time (safe)
$dbTime = '—';
if (isset($con) && $con) {
    if ($res = $con->query('SELECT NOW() AS dbtime')) {
        if ($row = $res->fetch_assoc()) $dbTime = date('h:i:s A', strtotime($row['dbtime']));
    }
}

// Cron last sync (newest)
$cronText = '—';

?>
<style>
/* --------- Clean professional metric cards --------- */
.metric-card{border:0;border-radius:1rem;box-shadow:0 6px 18px rgba(0,0,0,.06);transition:.2s ease;}
.metric-card:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.09);}
.metric-body{padding:1.1rem 1.1rem;}
.metric-header{display:flex;justify-content:space-between;align-items:center;gap:.75rem;}
.metric-title{font-size:.85rem;color:#6c757d;margin:0;}
.metric-value{font-weight:800;font-size:1.35rem;line-height:1.1;margin:0 0 .25rem;}
.metric-icon{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0}
.icon-primary{background:linear-gradient(135deg,#4f46e5,#06b6d4)}
.icon-warning{background:linear-gradient(135deg,#f59e0b,#ef4444)}
.icon-success{background:linear-gradient(135deg,#10b981,#059669)}
.icon-secondary{background:linear-gradient(135deg,#6b7280,#4b5563)}
.icon-dark{background:linear-gradient(135deg,#111827,#1f2937)}
.icon-danger{background:linear-gradient(135deg,#ef4444,#dc2626)}
.progress{height:6px;border-radius:999px;background:rgba(0,0,0,.08)}
.progress .progress-bar{border-radius:999px}
.text-muted{opacity:.9}
</style>

<div class="row g-3">
  <!-- Uptime -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div>
            <div class="metric-value text-primary"><?= htmlspecialchars($uptimePretty) ?></div>
            <div class="metric-title">System Uptime</div>
          </div>
          <div class="metric-icon icon-primary" title="Uptime">
            <i class="mdi mdi-clock-outline fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CPU Usage -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div class="w-100">
            <div class="metric-value text-dark"><?= $cpuPercent ?>%</div>
            <div class="metric-title mb-2">CPU Usage (5-min avg / <?= (int)$cores ?> cores)</div>
            <div class="progress">
              <div class="progress-bar <?= $cpuBarClass ?>" role="progressbar" style="width: <?= $cpuPercent ?>%;" aria-valuenow="<?= $cpuPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <div class="metric-icon icon-warning" title="CPU">
            <i class="mdi mdi-cpu-64-bit fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- RAM Usage -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div class="w-100">
            <div class="metric-value text-success"><?= htmlspecialchars($memText) ?></div>
            <div class="metric-title mb-2">RAM Usage (<?= $memPercent ?>%)</div>
            <div class="progress">
              <div class="progress-bar <?= $memBarClass ?>" role="progressbar" style="width: <?= $memPercent ?>%;" aria-valuenow="<?= $memPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
          <div class="metric-icon icon-success" title="Memory">
            <i class="mdi mdi-memory fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- System Time -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div>
            <div class="metric-value text-secondary"><?= $sysTime ?></div>
            <div class="metric-title"><?= $sysDate ?></div>
          </div>
          <div class="metric-icon icon-secondary" title="Server Time (Asia/Dhaka)">
            <i class="mdi mdi-calendar-clock fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- DB Time -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div>
            <div class="metric-value"><?= $dbTime ?></div>
            <div class="metric-title">DB Time</div>
          </div>
          <div class="metric-icon icon-dark" title="Database Clock">
            <i class="mdi mdi-database-clock-outline fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Last Cron Sync -->
  <div class="col-xl-4 col-md-6 col-12">
    <div class="card metric-card">
      <div class="metric-body">
        <div class="metric-header">
          <div>
            <div class="metric-value text-danger"><?= $cronText ?></div>
            <div class="metric-title">Last Cron Sync</div>
          </div>
          <div class="metric-icon icon-danger" title="Cron">
            <i class="mdi mdi-timer-sand fs-5"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end row -->