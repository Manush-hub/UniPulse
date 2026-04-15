// Simulate updating metrics
        function updateMetrics() {
            // In a real implementation, this would fetch data from the server
            document.getElementById('cpuValue').textContent = '65%';
            document.getElementById('memoryValue').textContent = '42%';
            document.getElementById('storageValue').textContent = '78%';
            document.getElementById('uptimeValue').textContent = '98%';
        }

        document.getElementById('refreshLogs').addEventListener('click', function() {
            // In a real implementation, this would fetch updated logs
            alert('Logs refreshed!');
        });

        // Initial load
        updateMetrics();