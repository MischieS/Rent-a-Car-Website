document.addEventListener("DOMContentLoaded", () => {
  // Sidebar Toggle
  const sidebarToggle = document.getElementById("sidebarToggle")
  const body = document.body

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      body.classList.toggle("sidebar-open")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    if (
      body.classList.contains("sidebar-open") &&
      !event.target.closest(".sidebar") &&
      !event.target.closest("#sidebarToggle")
    ) {
      body.classList.remove("sidebar-open")
    }
  })

  // Responsive sidebar behavior
  function handleResize() {
    if (window.innerWidth < 992) {
      body.classList.remove("sidebar-collapsed")
    }
  }

  // Initial call and event listener
  handleResize()
  window.addEventListener("resize", handleResize)

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  const bootstrap = window.bootstrap // Declare the bootstrap variable
  tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)")
  alerts.forEach((alert) => {
    setTimeout(() => {
      const closeButton = alert.querySelector(".btn-close")
      if (closeButton) {
        closeButton.click()
      }
    }, 5000)
  })
})
