import {
    Chart,
    DoughnutController,
    ArcElement,
    Tooltip,
    Legend,
} from 'chart.js';

// Register only what the dashboard doughnut chart needs.
Chart.register(DoughnutController, ArcElement, Tooltip, Legend);

let chartInstance = null;

/**
 * Render (or re-render) the incident-condition breakdown doughnut chart.
 *
 * The controller embeds the data as JSON in a #dashboard-chart-data script
 * tag; the canvas is #incident-condition-chart. Both are optional so this
 * module is a no-op on pages without a chart. Colors/text adapt to the
 * current theme and the chart re-themes live when dark mode is toggled.
 */
function renderDashboardChart() {
    const canvas = document.getElementById('incident-condition-chart');
    const dataEl = document.getElementById('dashboard-chart-data');

    if (!canvas || !dataEl) {
        return;
    }

    let payload;
    try {
        payload = JSON.parse(dataEl.textContent);
    } catch (e) {
        return;
    }

    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#cbd5e1' : '#475569'; // slate-300 / slate-600
    const borderColor = isDark ? '#0f172a' : '#ffffff'; // slate-900 / white

    const total = (payload.data || []).reduce((sum, n) => sum + n, 0);

    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: payload.labels || [],
            datasets: [
                {
                    data: payload.data || [],
                    backgroundColor: payload.colors || [],
                    borderWidth: 3,
                    borderColor,
                    hoverOffset: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: textColor,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { size: 13, family: 'Figtree, sans-serif' },
                    },
                },
                tooltip: {
                    backgroundColor: isDark ? '#1e293b' : '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#e2e8f0',
                    padding: 12,
                    cornerRadius: 10,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        label(context) {
                            const value = context.parsed || 0;
                            const pct = total ? Math.round((value / total) * 100) : 0;
                            return ` ${context.label}: ${value} (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', renderDashboardChart);
// Re-theme the chart when dark mode is toggled.
window.addEventListener('theme-changed', renderDashboardChart);
