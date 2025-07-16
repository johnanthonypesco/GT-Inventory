document.addEventListener('DOMContentLoaded', function() {
        // AI SUMMARY LOGIC
        // ===================================
        if (document.getElementById('ai-summary-section')) {
            let executiveSummaryData = @json($executiveSummaryData);
            let countdownInterval;

            const summaryContentEl = document.getElementById('ai-summary-content');
            const timerEl = document.getElementById('ai-summary-timer');
            const timerContainerEl = document.getElementById('ai-summary-timer-container');

            function renderSummary(summary) {
                if (!summary || !summary.kpis) {
                    summaryContentEl.innerHTML = `
                        <div class="summary-loader">
                           <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                           <span class="text-lg font-semibold">Generating new AI summary... Please wait.</span>
                        </div>`;
                    return;
                }
                
                let kpisHtml = summary.kpis.map(kpi => {
                    let trendIcon = '';
                    if (kpi.trend === 'up') trendIcon = `<span class="text-green-500"><i class="fas fa-arrow-up"></i></span>`;
                    else if (kpi.trend === 'down') trendIcon = `<span class="text-red-500"><i class="fas fa-arrow-down"></i></span>`;
                    
                    return `
                        <div class="kpi-card p-4 rounded-lg shadow">
                            <h4 class="text-sm font-medium text-gray-500 flex justify-between items-center">
                                ${kpi.label || 'N/A'} ${trendIcon}
                            </h4>
                            <p class="text-2xl font-semibold text-gray-900">${kpi.value || 'N/A'}</p>
                        </div>`;
                }).join('');

                if (summary.kpis.length === 0) kpisHtml = `<div class="kpi-card p-4 rounded-lg shadow col-span-3"><p class="text-gray-700">No KPIs available.</p></div>`;

                let anomaliesHtml = summary.anomalies.map(anomaly => {
                    let iconClass, iconColor;
                    if (anomaly.type === 'positive') { iconClass = 'fa-arrow-up'; iconColor = 'text-green-500'; }
                    else if (anomaly.type === 'negative') { iconClass = 'fa-arrow-down'; iconColor = 'text-red-500'; }
                    else { iconClass = 'fa-exclamation-circle'; iconColor = 'text-yellow-500'; }
                    
                    return `
                        <div class="anomaly-item ${anomaly.type} bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3">
                            <i class="fas ${iconClass} ${iconColor} mt-1"></i>
                            <p class="text-sm text-gray-700">${anomaly.message || 'No message.'}</p>
                        </div>`;
                }).join('');

                if (summary.anomalies.length === 0) anomaliesHtml = `<div class="anomaly-item positive bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3"><i class="fas fa-check-circle text-green-500 mt-1"></i><p class="text-sm text-green-700">No critical anomalies detected.</p></div>`;

                let recommendationsHtml = summary.recommendations.map(rec => `
                    <div class="recommendation-card text-white p-4 rounded-lg shadow-lg flex items-start gap-3">
                        <i class="fas fa-lightbulb mt-1"></i>
                        <p class="font-medium flex-1">${rec.message || 'No message.'}</p>
                    </div>`).join('');
                
                if (summary.recommendations.length === 0) recommendationsHtml = `<div class="bg-gray-50 p-4 rounded-lg"><p class="text-sm text-gray-600">No specific recommendations.</p></div>`;

                summaryContentEl.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">${kpisHtml}</div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>AI Anomaly Detection</h3>
                            <div class="space-y-3">${anomaliesHtml}</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-lightbulb mr-2 text-blue-500"></i>AI Recommendations</h3>
                            <div class="space-y-3">${recommendationsHtml}</div>
                        </div>
                    </div>`;
            }

            function startCountdown(expiryDateString) {
                if (countdownInterval) clearInterval(countdownInterval);
                if (!expiryDateString) {
                    timerContainerEl.style.display = 'none';
                    return;
                }
                timerContainerEl.style.display = 'block';

                const expiryDate = new Date(expiryDateString);

                countdownInterval = setInterval(() => {
                    const diff = expiryDate - new Date();
                    if (diff <= 0) {
                        clearInterval(countdownInterval);
                        timerEl.textContent = 'Expired. Getting new summary...';
                        getNewSummary();
                        return;
                    }
                    const minutes = Math.floor((diff / 1000 / 60) % 60);
                    const seconds = Math.floor((diff / 1000) % 60);
                    timerEl.textContent = `Request limit resets in: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }, 1000);
            }

            async function getNewSummary() {
                if (window.isFetchingSummary) return;
                window.isFetchingSummary = true;
                renderSummary(null);
                startCountdown(null);

                try {
                    const response = await fetch("{{ route('admin.generate.ai.summary') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    });
                    if (!response.ok) throw new Error('Failed to fetch from server.');
                    executiveSummaryData = await response.json();
                    renderSummary(executiveSummaryData.summary);
                    startCountdown(executiveSummaryData.expires_at);
                } catch (error) {
                    console.error("Error fetching AI summary:", error);
                    renderSummary({
                        kpis: [{label: 'System Status', value: 'Error', trend: 'stable'}],
                        anomalies: [{type: 'negative', message: 'Could not fetch summary.'}],
                        recommendations: [{message: 'Please refresh page to try again.'}]
                    });
                    timerEl.textContent = 'Error fetching data.';
                } finally {
                    window.isFetchingSummary = false;
                }
            }
            
            if (executiveSummaryData && new Date(executiveSummaryData.expires_at) > new Date()) {
                renderSummary(executiveSummaryData.summary);
                startCountdown(executiveSummaryData.expires_at);
            } else {
                getNewSummary();
            }
        }

        // CHARTING LOGIC
        // ===================================
        // Global Chart Instances
        let revenueChart, productPerformanceChart, deductedQuantitiesChart, inventoryLevelsChart,
            seasonalTrendsChart, orderStatusChart;

        const chartsContainer = document.getElementById('chartsContainer');
        const leftCharts = document.getElementById('leftCharts');
        const rightCharts = document.getElementById('rightCharts');

        function updateChartDisplay(option) {
            document.querySelectorAll('.chart-container').forEach(chart => {
                chart.style.display = 'none';
                chart.classList.remove('col-span-2');
                const canvasContainer = chart.querySelector('.chart-canvas');
                if(canvasContainer) canvasContainer.classList.remove('chart-full', 'chart-half');
            });

            leftCharts.style.display = 'block';
            rightCharts.style.display = 'block';
            if (chartsContainer) chartsContainer.style.display = 'grid';

            const chartSelectors = {
                revenue: '.revenue-chart',
                deductions: '.deductions-chart',
                performance: '.performance-chart',
                inventory: '.inventory-chart',
                trends: '.trends-chart',
                orderStatus: '.orderStatus-chart',
            };

            if (option === 'all') {
                document.querySelectorAll('.chart-container').forEach(chart => {
                    chart.style.display = 'block';
                    const canvasContainer = chart.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-half');
                });
            } else if (chartSelectors[option]) {
                const chartElement = document.querySelector(chartSelectors[option]);
                if (chartElement) {
                    chartElement.style.display = 'block';
                    chartElement.classList.add('col-span-2');
                    const canvasContainer = chartElement.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-full');

                    if (['revenue', 'deductions', 'inventory'].includes(option)) {
                        rightCharts.style.display = 'none';
                    } else if (['performance', 'trends', 'orderStatus'].includes(option)){
                        leftCharts.style.display = 'none';
                    }
                }
            }
        }

        function updateCustomChartDisplay(selectedCharts) {
            document.querySelectorAll('.chart-container').forEach(chart => {
                chart.style.display = 'none';
                chart.classList.remove('col-span-2');
                const canvasContainer = chart.querySelector('.chart-canvas');
                if(canvasContainer) canvasContainer.classList.remove('chart-full', 'chart-half');
            });

            let leftVisible = false;
            let rightVisible = false;

            selectedCharts.forEach(chartType => {
                const chart = document.querySelector(`.${chartType}-chart`);
                if (chart) {
                    chart.style.display = 'block';
                    if (leftCharts.contains(chart)) leftVisible = true;
                    if (rightCharts.contains(chart)) rightVisible = true;
                }
            });

            if (selectedCharts.length === 1) {
                const chart = document.querySelector(`.${selectedCharts[0]}-chart`);
                if (chart) {
                    chart.classList.add('col-span-2');
                    const canvasContainer = chart.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-full');
                }
                if (leftCharts.contains(chart)) rightCharts.style.display = 'none';
                if (rightCharts.contains(chart)) leftCharts.style.display = 'none';
            } else if (selectedCharts.length > 1) {
                document.querySelectorAll('.chart-container').forEach(chart => {
                    if (chart.style.display === 'block') {
                        const canvasContainer = chart.querySelector('.chart-canvas');
                        if(canvasContainer) canvasContainer.classList.add('chart-half');
                    }
                });
                leftCharts.style.display = leftVisible ? 'block' : 'none';
                rightCharts.style.display = rightVisible ? 'block' : 'none';
            } else {
                leftCharts.style.display = 'none';
                rightCharts.style.display = 'none';
            }
             if(chartsContainer) chartsContainer.style.display = (leftVisible || rightVisible) ? 'grid' : 'none';
        }

        const chartFilter = document.getElementById('chartFilter');
        if(chartFilter) {
            chartFilter.addEventListener('change', function() {
                const selectedOption = this.value;
                const customSelection = document.getElementById('customChartSelection');
                if (selectedOption === 'custom') {
                    if(customSelection) customSelection.classList.remove('hidden');
                } else {
                    if(customSelection) customSelection.classList.add('hidden');
                    updateChartDisplay(selectedOption);
                }
            });
        }

        const applyCustomCharts = document.getElementById('applyCustomCharts');
        if(applyCustomCharts) {
            applyCustomCharts.addEventListener('click', function() {
                const selectedCharts = Array.from(document.querySelectorAll('input[name="customChart"]:checked')).map(el => el.value);
                updateCustomChartDisplay(selectedCharts);
            });
        }

        // --- Revenue Chart ---
        const revenueChartCtx = document.getElementById('revenueChart');
        if (revenueChartCtx) {
            revenueChart = new Chart(revenueChartCtx.getContext('2d'), {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Revenue (Delivered Orders)', data: [], borderColor: 'rgba(59, 130, 246, 1)', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2, tension: 0.3, pointBackgroundColor: 'rgba(59, 130, 246, 1)', pointRadius: 3, pointHoverRadius: 5, fill: true }] },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (c) => `Revenue: ₱${c.raw.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}` } } },
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Revenue (₱)' }, ticks: { callback: (v) => '₱' + v.toLocaleString('en-PH') } }, x: { title: { display: true, text: 'Time Period' }, grid: { display: false } } },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }
        
        const revenueTimePeriod = document.getElementById('revenueTimePeriod');
        if(revenueTimePeriod) revenueTimePeriod.addEventListener('change', function() {
            const period = this.value;
            document.getElementById('revenueMonthContainer').classList.toggle('hidden', period === 'year');
            document.getElementById('revenueWeekContainer').classList.toggle('hidden', period !== 'week');
            if (period === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueMonthFilter')?.addEventListener('change', function() {
            if (document.getElementById('revenueTimePeriod')?.value === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueYearFilter')?.addEventListener('change', function() {
            if (document.getElementById('revenueTimePeriod')?.value === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueUpdateBtn')?.addEventListener('click', updateRevenueChart);

        function updateWeekOptions(chartPrefix) {
            const year = document.getElementById(`${chartPrefix}YearFilter`)?.value;
            const month = document.getElementById(`${chartPrefix}MonthFilter`)?.value;
            if(!year || !month) return;
            const date = new Date(year, month - 1, 1);
            const daysInMonth = new Date(year, month, 0).getDate();
            let weeksInMonth = Math.ceil((daysInMonth + date.getDay()) / 7);
            const weekSelect = document.getElementById(`${chartPrefix}WeekFilter`);
            if(!weekSelect) return;
            weekSelect.innerHTML = '';
            for (let i = 1; i <= weeksInMonth; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Week ${i}`;
                weekSelect.appendChild(option);
            }
        }

        function updatePeriodDisplay(chartPrefix) {
            const period = document.getElementById(`${chartPrefix}TimePeriod`)?.value;
            const year = document.getElementById(`${chartPrefix}YearFilter`)?.value;
            let displayText = '';
            switch(period) {
                case 'day':
                case 'week':
                    const month = document.getElementById(`${chartPrefix}MonthFilter`)?.value;
                    displayText = `${new Date(year, month - 1, 1).toLocaleString('default', { month: 'long' })} ${year}`;
                    break;
                case 'month': displayText = `Year ${year}`; break;
                case 'year': displayText = 'Multiple Years'; break;
            }
            const currentPeriodEl = document.getElementById('currentPeriod');
            if(currentPeriodEl) currentPeriodEl.textContent = displayText;
        }

        async function updateRevenueChart() {
            const period = document.getElementById('revenueTimePeriod')?.value;
            const year = document.getElementById('revenueYearFilter')?.value;
            const month = (period !== 'year') ? document.getElementById('revenueMonthFilter')?.value : '';
            const week = (period === 'week') ? document.getElementById('revenueWeekFilter')?.value : '';
            const btn = document.getElementById('revenueUpdateBtn');
            if(!btn || !revenueChart) return;

            btn.disabled = true;
            btn.innerHTML = `<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...</span>`;
            try {
                let url = `/admin/revenue-data/${period}/${year}`;
                if (month) url += `/${month}`;
                if (week) url += `/${week}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response error');
                const data = await response.json();
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.values;
                revenueChart.options.scales.x.title.text = period.charAt(0).toUpperCase() + period.slice(1);
                document.getElementById('totalRevenue').textContent = `₱${data.total.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                document.getElementById('avgRevenue').textContent = `₱${data.average.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                revenueChart.update();
            } catch (error) {
                console.error('Error fetching revenue data:', error);
                Swal.fire('Error', 'Failed to load revenue data.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Update Chart';
            }
        }
        if(document.getElementById('revenueChart')) {
            updatePeriodDisplay('revenue');
            updateRevenueChart();
        }

        // --- Product Performance Chart ---
        const perfChartCtx = document.getElementById('productPerformanceChart');
        if(perfChartCtx) {
            productPerformanceChart = new Chart(perfChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: @json($labels), datasets: [{ label: 'Quantity of Products Sold', data: @json($data), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity Sold' } }, x: { title: { display: true, text: 'Product Generic Name' } } } }
            });
            const performanceData = {
                mostSold: { labels: @json($labels), data: @json($data), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', label: 'Most Ordered Products' },
                moderateSold: { labels: @json($moderateSoldLabels), data: @json($moderateSoldData), backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', label: 'Moderately Ordered Products' },
                lowSold: { labels: @json($lowSoldLabels), data: @json($lowSoldData), backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', label: 'Low Ordered Products' }
            };
            function updatePerformanceChart(type) {
                productPerformanceChart.data.labels = performanceData[type].labels;
                productPerformanceChart.data.datasets[0].data = performanceData[type].data;
                productPerformanceChart.data.datasets[0].backgroundColor = performanceData[type].backgroundColor;
                productPerformanceChart.data.datasets[0].borderColor = performanceData[type].borderColor;
                productPerformanceChart.data.datasets[0].label = performanceData[type].label;
                productPerformanceChart.update();
            }
            document.getElementById('mostSoldBtn')?.addEventListener('click', () => updatePerformanceChart('mostSold'));
            document.getElementById('moderateSoldBtn')?.addEventListener('click', () => updatePerformanceChart('moderateSold'));
            document.getElementById('lowSoldBtn')?.addEventListener('click', () => updatePerformanceChart('lowSold'));
        }

        // --- Product Delivered Chart (Deducted Quantities) ---
        const deductedChartCtx = document.getElementById('deductedQuantitiesChart');
        if(deductedChartCtx) {
            deductedQuantitiesChart = new Chart(deductedChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: @json($deductedLabels), datasets: [{ label: 'Quantity Delivered', data: @json($deductedData), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity Delivered' } }, x: { title: { display: true, text: 'Product (Generic Name)' } } } }
            });
            async function updateDeductedChart() {
                const year = document.getElementById('deductedYearFilter')?.value;
                const month = document.getElementById('deductedMonthFilter')?.value;
                const location = document.getElementById('deductedLocationFilter')?.value || '';
                if(!year || !month || !deductedQuantitiesChart) return;
                try {
                    const response = await fetch(`/admin/filtered-deducted-quantities/${year}/${month}/${location}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    deductedQuantitiesChart.data.labels = data.labels;
                    deductedQuantitiesChart.data.datasets[0].data = data.deductedData;
                    deductedQuantitiesChart.update();
                } catch (error) {
                    console.error('Error fetching deducted quantities:', error);
                }
            }
            document.getElementById('deductedYearFilter')?.addEventListener('change', updateDeductedChart);
            document.getElementById('deductedMonthFilter')?.addEventListener('change', updateDeductedChart);
            document.getElementById('deductedLocationFilter')?.addEventListener('change', updateDeductedChart);
            updateDeductedChart();
        }

        // --- Inventory Levels Chart ---
        const invChartCtx = document.getElementById('inventoryLevelsChart');
        if (invChartCtx) {
            inventoryLevelsChart = new Chart(invChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Current Stock', data: [], backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Total Quantity in Stock' } }, x: { title: { display: true, text: 'Product Generic Name' } } } }
            });
            async function updateInventoryChart() {
                const year = document.getElementById('inventoryYearFilter')?.value;
                const month = document.getElementById('inventoryMonthFilter')?.value;
                const location = document.getElementById('inventoryLocationFilter')?.value || '';
                if(!year || !month || !inventoryLevelsChart) return;
                try {
                    const response = await fetch(`/admin/inventory-levels/${year}/${month}/${location}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    inventoryLevelsChart.data.labels = data.labels;
                    inventoryLevelsChart.data.datasets[0].data = data.inventoryData;
                    inventoryLevelsChart.update();
                } catch (error) {
                    console.error('Error fetching inventory data:', error);
                }
            }
            document.getElementById('inventoryYearFilter')?.addEventListener('change', updateInventoryChart);
            document.getElementById('inventoryMonthFilter')?.addEventListener('change', updateInventoryChart);
            document.getElementById('inventoryLocationFilter')?.addEventListener('change', updateInventoryChart);
            updateInventoryChart();
        }

        // --- Seasonal Trends Chart ---
        const trendsChartCtx = document.getElementById('seasonalTrendsChart');
        if(trendsChartCtx) {
            seasonalTrendsChart = new Chart(trendsChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [ { label: 'Current Sales', data: [], backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }, { label: 'Next Month Predicted', data: [], backgroundColor: 'rgba(255, 159, 64, 0.6)', borderColor: 'rgba(255, 159, 64, 1)', borderWidth: 1 }, { label: 'Historical Average', data: [], backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1, type: 'line', tension: 0.3 } ] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Sales Quantity' } }, x: { title: { display: true, text: 'Products' } } }, plugins: { tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${Math.round(c.raw)}` } }, legend: { position: 'top' } } }
            });
            async function fetchAndUpdateTrendData() {
                const season = document.getElementById('seasonFilter')?.value;
                const year = document.getElementById('trendYearFilter')?.value;
                const container = document.getElementById('predictionCardsContainer');
                if(!season || !year || !container || !seasonalTrendsChart) return;
                container.innerHTML = '<div class="text-center py-4 col-span-3"><div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div><p class="mt-2 text-gray-600">Loading data...</p></div>';
                try {
                    const response = await fetch(`/admin/trending-products?season=${season}&year=${year}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    seasonalTrendsChart.data.labels = data.trending_products.map(p => p.generic_name);
                    seasonalTrendsChart.data.datasets[0].data = data.trending_products.map(p => p.current_sales);
                    seasonalTrendsChart.data.datasets[1].data = data.trending_products.map(p => p.next_month_prediction);
                    seasonalTrendsChart.data.datasets[2].data = data.trending_products.map(p => p.historical_avg);
                    seasonalTrendsChart.update();
                    container.innerHTML = '';
                    if (data.predicted_peaks.length === 0) {
                        container.innerHTML = '<div class="col-span-3 text-center py-4 text-gray-500">No products found.</div>';
                        return;
                    }
                    data.predicted_peaks.forEach(p => {
                        const percent = Math.round(p.prediction_percent);
                        const trendArrow = percent >= 60 ? '↑' : percent >= 30 ? '→' : '↓';
                        const trendColor = percent >= 60 ? 'text-green-600' : percent >= 30 ? 'text-yellow-600' : 'text-red-600';
                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded-lg shadow border-l-4 border-blue-500 hover:shadow-md transition-shadow';
                        card.innerHTML = `<div class="flex justify-between items-start"><h4 class="font-medium text-gray-800">${p.generic_name}</h4><span class="text-xs font-semibold ${trendColor}">${trendArrow}</span></div><p class="text-sm text-gray-600 mt-1"><span class="font-medium">Season:</span> ${p.season_peak === 'tag-init' ? 'Summer' : p.season_peak === 'tag-ulan' ? 'Rainy' : 'All Year'}</p><div class="mt-2 flex items-center"><div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full" style="width: ${percent}%"></div></div><span class="ml-2 text-xs font-semibold text-blue-600">${percent}%</span></div><div class="mt-2 grid grid-cols-2 gap-2 text-xs"><div class="bg-blue-50 p-1 rounded text-center"><span class="font-medium">Current:</span> ${Math.round(p.current_sales)}</div><div class="bg-orange-50 p-1 rounded text-center"><span class="font-medium">Predicted:</span> ${Math.round(p.next_month_prediction)}</div></div>`;
                        container.appendChild(card);
                    });
                } catch (err) {
                    console.error('Failed to load trend data:', err);
                    Swal.fire('Error', 'Failed to load trend data.', 'error');
                }
            }
            document.getElementById('seasonFilter')?.addEventListener('change', fetchAndUpdateTrendData);
            document.getElementById('trendYearFilter')?.addEventListener('change', fetchAndUpdateTrendData);
            fetchAndUpdateTrendData();
        }

        // --- Order Status Distribution Chart ---
        const statusChartCtx = document.getElementById('orderStatusChart');
        if(statusChartCtx) {
            orderStatusChart = new Chart(statusChartCtx.getContext('2d'), {
                type: 'doughnut',
                data: { labels: ['Delivered', 'Pending', 'Cancelled'], datasets: [{ label: 'Order Count', data: [{{$orderStatusCounts['delivered']??0}}, {{$orderStatusCounts['pending']??0}}, {{$orderStatusCounts['cancelled']??0}}], backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 205, 86, 0.8)', 'rgba(255, 99, 132, 0.8)'], borderColor: ['#fff'], borderWidth: 2 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (c) => { const total = c.dataset.data.reduce((a,b) => a+b, 0); const perc = total > 0 ? (c.raw/total*100).toFixed(1)+'%' : '0%'; return `${c.label}: ${c.raw} (${perc})`; } } } } }
            });
        }
        
        if(document.getElementById('chartFilter')) {
            updateChartDisplay(document.getElementById('chartFilter').value);
        }

        // --- AI Analysis Logic ---
        const aiAnalysisResultDiv = document.getElementById('aiAnalysisResult');
        const aiModelNameSpan = document.getElementById('aiModelName');
        const analyzeChartsBtn = document.getElementById('analyzeChartsBtn');
        const speakAnalysisBtn = document.getElementById('speakAnalysisBtn');

        if(analyzeChartsBtn) {
            function speakText(text) {
                if ('speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-US';
                    window.speechSynthesis.speak(utterance);
                } else {
                    Swal.fire('Browser Not Supported', 'Text-to-speech is not supported.', 'info');
                }
            }
            analyzeChartsBtn.addEventListener('click', async function() {
                if(!aiAnalysisResultDiv || !aiModelNameSpan || !speakAnalysisBtn) return;
                aiAnalysisResultDiv.querySelector('p').innerHTML = '<div class="flex items-center justify-center"><div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-purple-500"></div><p class="ml-2 text-purple-600">Getting AI analysis...</p></div>';
                aiModelNameSpan.textContent = '';
                speakAnalysisBtn.classList.add('hidden');
                
                const chartsToAnalyze = {};
                document.querySelectorAll('.chart-container').forEach(container => {
                    if (container.style.display === 'block') {
                        const chartName = container.querySelector('h3')?.textContent || 'Unknown Chart';
                        let chartInstance;
                        if (container.classList.contains('revenue-chart')) chartInstance = revenueChart;
                        else if (container.classList.contains('deductions-chart')) chartInstance = deductedQuantitiesChart;
                        else if (container.classList.contains('inventory-chart')) chartInstance = inventoryLevelsChart;
                        else if (container.classList.contains('performance-chart')) chartInstance = productPerformanceChart;
                        else if (container.classList.contains('trends-chart')) chartInstance = seasonalTrendsChart;
                        else if (container.classList.contains('orderStatus-chart')) chartInstance = orderStatusChart;
                        
                        if (chartInstance) {
                            chartsToAnalyze[chartName] = { labels: chartInstance.data.labels, values: chartInstance.data.datasets.map(d => d.data) };
                        }
                    }
                });

                if (Object.keys(chartsToAnalyze).length === 0) {
                    aiAnalysisResultDiv.querySelector('p').innerHTML = '<p class="text-gray-500">No charts displayed.</p>';
                    return;
                }

                try {
                    const response = await fetch("{{ url('/admin/analyze-charts') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(chartsToAnalyze)
                    });
                    if (!response.ok) {
                        // Handle auth errors from this AJAX call as well
                         if (response.status === 401 || response.status === 419) {
                            Swal.fire('Session Expired', 'Please log in again.', 'warning').then(() => window.location.href = "{{ route('login') }}");
                            return;
                        }
                        const errorData = await response.json();
                        throw new Error(errorData.error || `HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    aiAnalysisResultDiv.querySelector('p').innerHTML = data.analysis;
                    aiModelNameSpan.textContent = `Analysis by: ${data.model}`;
                    speakAnalysisBtn.classList.remove('hidden');
                } catch (error) {
                    console.error('Error fetching AI analysis:', error);
                    aiAnalysisResultDiv.querySelector('p').innerHTML = `<p class="text-red-600">Error: ${error.message}.</p>`;
                    speakAnalysisBtn.classList.add('hidden');
                }
            });

            speakAnalysisBtn.addEventListener('click', function() {
                const analysisText = aiAnalysisResultDiv.querySelector('p').textContent;
                if (analysisText && !analysisText.includes('Loading...')) {
                    speakText(analysisText);
                }
            });
        }
        
        // Geolocation for staff
        @if(auth()->guard('staff')->check())
        if (navigator.geolocation) {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        fetch("{{ route('api.update-location') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude }),
                        });
                    },
                    function (error) { console.error("Geolocation is not supported."); }
                );
            }, 10000);
        } else { console.error("Geolocation is not supported."); }
        @endif
    });