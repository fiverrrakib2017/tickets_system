<div class="row">
    <!-- Interface List -->
    <div class="col-md-4">
        <h6 class="mb-2">Interfaces</h6>
        <ul class="list-group" id="_interface_list">
            <!-- AJAX will load -->
        </ul>
    </div>

    <!-- Graph Area -->
    <div class="col-md-8">
        <h6 class="mb-2">Bandwidth Graph</h6>
        <canvas id="bandwidthChart" height="120"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;

function _load_in_interface() {
    fetch('ajax/mikrotik_interfaces.php?customer_id=<?= (int)$customer['id'] ?>')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(iface => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center interface-item"
                        data-interface="${iface.name}">
                        ${iface.name}
                        <span class="badge bg-primary">View</span>
                    </li>`;
            });
            document.getElementById('_interface_list').innerHTML = html;
        });
}

function loadGraph(interfaceName) {
    fetch('ajax/mikrotik_bandwidth.php?customer_id=<?= (int)$customer['id'] ?>&interface=' + interfaceName)
        .then(res => res.json())
        .then(data => {

            if (chart) chart.destroy();

            chart = new Chart(document.getElementById('bandwidthChart'), {
                type: 'line',
                data: {
                    labels: data.time,
                    datasets: [
                        { label: 'Download (Rx)', data: data.rx, tension: 0.4 },
                        { label: 'Upload (Tx)', data: data.tx, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            ticks: {
                                callback: v => (v / 1024 / 1024).toFixed(2) + ' Mbps'
                            }
                        }
                    }
                }
            });
        });
}

// click event
document.addEventListener('click', function(e){
    if(e.target.closest('.interface-item')){
        loadGraph(e.target.closest('.interface-item').dataset.interface);
    }
});
/*--------------Call Interface Function---------------*/
_load_in_interface();
</script>
