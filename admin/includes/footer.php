<!-- Bootstrap core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin JS -->
    <script src="assets/js/admin.js"></script>
    
    <!-- Custom scripts for this page -->
    <script>
        // Initialize any page-specific components
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const sidebarToggle = document.getElementById('sidebarCollapseBtn');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                    document.querySelector('main').classList.toggle('expanded');
                });
            }
        });
    </script>
</body>
</html>
