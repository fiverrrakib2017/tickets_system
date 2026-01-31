
<div class="row">
    <!-- Interface Tabs -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white py-2">
                <strong>Interfaces</strong>
            </div>

            <div class="card-body p-0" style="max-height:400px; overflow-y:auto;">
                <ul class="nav flex-column nav-pills" id="_interface_list" role="tablist">
                    <!-- Interfaces will load here -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Graph Area -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light py-2 d-flex justify-content-between">
                <strong id="selectedInterfaceTitle">Bandwidth Graph</strong>
                <span class="badge bg-success text-white" id="interfaceStatus">Select Interface</span>
            </div>

            <div class="card-body">
                <canvas id="bandwidthChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Graph Modal -->
<div class="modal fade" id="graphModal" tabindex="-1">
  <div class="modal-dialog  modal-dialog-scrollable">
    <div class="modal-content  border-0">

      <!-- Header -->
      <div class="modal-header bg-dark text-white py-2">
        <h5 class="modal-title" id="graphTitle">
            Interface Bandwidth Graphs
        </h5>
        <button type="button" class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body bg-light">

        <!-- Daily -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white py-2">
                <strong>Daily Traffic</strong>
            </div>

            <div class="card-body text-center">
                <img id="graph_day" class="img-fluid rounded mb-2">

                <!-- 👇 Daily Graph Text -->
                <div id="graph_day_info"
                    class="small text-start text-muted border-top pt-2">
                Loading...
                </div>
            </div>
        </div>


        <!-- Weekly -->
        <div class="card mb-4 shadow-sm">
          <div class="card-header bg-info text-white py-2">
            <strong>Weekly Traffic</strong>
          </div>
          <div class="card-body text-center">
            <img id="graph_week" class="img-fluid rounded">
                <div id="graph_week_info"
                        class="small text-start text-muted border-top pt-2">
                    Loading...
                </div>
          </div>
        </div>

        <!-- Monthly -->
        <div class="card mb-4 shadow-sm">
          <div class="card-header bg-success text-white py-2">
            <strong>Monthly Traffic</strong>
          </div>
          <div class="card-body text-center">
            <img id="graph_month" class="img-fluid rounded">
               <div id="graph_month_info"
                        class="small text-start text-muted border-top pt-2">
                    Loading...
                </div>
          </div>
        </div>

        <!-- Yearly -->
        <div class="card mb-2 shadow-sm">
          <div class="card-header bg-secondary text-white py-2">
            <strong>Yearly Traffic</strong>
          </div>
          <div class="card-body text-center">
            <img id="graph_year" class="img-fluid rounded">
                <div id="graph_year_info"
                            class="small text-start text-muted border-top pt-2">
                      Loading...
                 </div>
          </div>
        </div>



      </div>

      <!-- Footer -->
      <div class="modal-footer bg-white">
        <small class="text-muted">
            Bandwidth data generated from router (SNMP / MRTG)
        </small>
        <button type="button" class="btn btn-outline-danger btn-sm"
                data-bs-dismiss="modal">
            Close
        </button>
      </div>

    </div>
  </div>
</div>



<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;
let chartInterval;
const maxPoints = 30; 
let selectedInterface = null;

/* ---------------- Load Interface List ----------------*/
function _load_in_interface() {
    fetch('ajax/mikrotik_interfaces.php?customer_id=<?= (int)$customer['id'] ?>')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(iface => {
                const graphUrl = `http://103.112.204.48:8082/graphs/iface/${iface}/`;
                
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center interface-item"
                        data-interface="${iface}" data-graph-url="${graphUrl}">
                        ${iface}
                        <button type="button" class="btn btn-sm btn-primary show_graph"><i class="fas fa-eye"></i></button>
                    </li>`;
            });

            document.getElementById('_interface_list').innerHTML = html;
        })
        .catch(err => console.error('Interface load error:', err));
}

/*---------------- Initialize Chart ----------------*/ 
function initChart() {
    const ctx = document.getElementById('bandwidthChart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                { label: 'Download (Rx)', data: [], borderColor: 'blue', tension: 0.4, fill: false },
                { label: 'Upload (Tx)', data: [], borderColor: 'red', tension: 0.4, fill: false }
            ]
        },
        options: {
            responsive: true,
            animation: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: true } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => v.toFixed(2) + ' Mbps' }
                },
                x: { ticks: { autoSkip: true, maxTicksLimit: 10 } }
            }
        }
    });
}

/* ---------------- Update Chart per second ----------------*/
function updateGraph() {
    if (!selectedInterface) return;

    fetch('ajax/mikrotik_bandwidth.php?customer_id=<?= (int)$customer['id'] ?>&interface=' + selectedInterface)
        .then(res => res.json())
        .then(data => {
            const time = data.time[0];
            const rx = data.rx[0];
            const tx = data.tx[0];

            chart.data.labels.push(time);
            chart.data.datasets[0].data.push(rx);
            chart.data.datasets[1].data.push(tx);

            // Keep last maxPoints
            if (chart.data.labels.length > maxPoints) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
                chart.data.datasets[1].data.shift();
            }

            chart.update();

            /*-------------Update header badge------------*/ 
            document.getElementById('selectedInterfaceTitle').innerText = selectedInterface;
            document.getElementById('interfaceStatus').innerText = 'Online';
        })
        .catch(err => {
            console.error('Graph update error:', err);
            document.getElementById('interfaceStatus').innerText = 'Error';
        });
}

/* ---------------- Handle Interface Click ----------------*/
document.addEventListener('click', function(e){
    const li = e.target.closest('.interface-item');
    if(li){
        selectedInterface = li.dataset.interface;

        /*--------Clear previous chart data----------*/ 
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.data.datasets[1].data = [];
        chart.update();

        /*--------Clear previous interval----*/ 
        if(chartInterval) clearInterval(chartInterval);

        /*---------Start live update every 1 second-----------*/ 
        updateGraph();
        chartInterval = setInterval(updateGraph, 1000);
    }
});
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.show_graph');
    if (!btn) return;

    const li = btn.closest('.interface-item');
    const iface = li.dataset.interface;

    document.getElementById('graphTitle').innerText =
        iface + ' Bandwidth Graphs';

    // ===== Graph Images =====
    document.getElementById('graph_day').src =
        'include/graph_proxy.php?interface=' + iface + '&file=daily.gif';

    document.getElementById('graph_week').src =
        'include/graph_proxy.php?interface=' + iface + '&file=weekly.gif';

    document.getElementById('graph_month').src =
        'include/graph_proxy.php?interface=' + iface + '&file=monthly.gif';

    document.getElementById('graph_year').src =
        'include/graph_proxy.php?interface=' + iface + '&file=yearly.gif';

    // ===== Graph Text (per graph) =====
    fetch('include/graph_text_proxy.php?interface=' + iface + '&period=day')
        .then(r => r.text())
        .then(t => document.getElementById('graph_day_info').innerHTML = t);

    fetch('include/graph_text_proxy.php?interface=' + iface + '&period=week')
        .then(r => r.text())
        .then(t => document.getElementById('graph_week_info').innerHTML = t);

    fetch('include/graph_text_proxy.php?interface=' + iface + '&period=month')
        .then(r => r.text())
        .then(t => document.getElementById('graph_month_info').innerHTML = t);

    fetch('include/graph_text_proxy.php?interface=' + iface + '&period=year')
        .then(r => r.text())
        .then(t => document.getElementById('graph_year_info').innerHTML = t);

    new bootstrap.Modal(document.getElementById('graphModal')).show();
});




/*---------------- Initialize ----------------*/ 
_load_in_interface();
initChart();
</script>
