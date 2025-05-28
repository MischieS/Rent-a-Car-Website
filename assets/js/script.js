// Custom JavaScript for Rent a Car website

document.addEventListener("DOMContentLoaded", () => {
  // Initialize AOS animations
  const AOS = window.AOS // Declare the AOS variable
  if (AOS) {
    AOS.init({
      duration: 800,
      easing: "ease-in-out",
      once: true,
    })
  }

  // Back to top button
  const backToTopButton = document.getElementById("backToTop")

  if (backToTopButton) {
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTopButton.classList.add("active")
      } else {
        backToTopButton.classList.remove("active")
      }
    })

    backToTopButton.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({ top: 0, behavior: "smooth" })
    })
  }

  // Sticky header
  const header = document.querySelector(".main-header")

  if (header) {
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 100) {
        header.classList.add("sticky-header")
      } else {
        header.classList.remove("sticky-header")
      }
    })
  }

  // Form validation
  const searchForm = document.getElementById("searchForm")

  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      const pickupDate = new Date(document.querySelector('input[name="pickup_date"]').value)
      const returnDate = new Date(document.querySelector('input[name="return_date"]').value)
      const pickupTime = document.querySelector('input[name="pickup_time"]').value
      const returnTime = document.querySelector('input[name="return_time"]').value

      if (returnDate < pickupDate) {
        e.preventDefault()
        alert("Return date cannot be before pickup date")
        return false
      }

      if (returnDate.getTime() === pickupDate.getTime() && returnTime < pickupTime) {
        e.preventDefault()
        alert("Return time must be after pickup time on the same day")
        return false
      }

      return true
    })
  }

  // Set default dates (today and tomorrow)
  const dateInputs = document.querySelectorAll('input[type="date"]')
  const timeInputs = document.querySelectorAll('input[type="time"]')

  if (dateInputs.length > 0) {
    const today = new Date()
    const tomorrow = new Date(today)
    tomorrow.setDate(tomorrow.getDate() + 1)

    // Format dates for input fields
    const formatDate = (date) => {
      return date.toISOString().split("T")[0]
    }

    // Set default values
    if (dateInputs[0]) dateInputs[0].value = formatDate(today)
    if (dateInputs[1]) dateInputs[1].value = formatDate(tomorrow)
    if (timeInputs[0]) timeInputs[0].value = "10:00"
    if (timeInputs[1]) timeInputs[1].value = "10:00"
  }

  // Add smooth scrolling for all links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href")

      if (href !== "#") {
        e.preventDefault()

        const targetElement = document.querySelector(href)

        if (targetElement) {
          targetElement.scrollIntoView({
            behavior: "smooth",
          })
        }
      }
    })
  })

  // Handle price range inputs on the car listing page
  const minPriceInput = document.getElementById("min_price")
  const maxPriceInput = document.getElementById("max_price")

  if (minPriceInput && maxPriceInput) {
    // Ensure min price doesn't exceed max price
    minPriceInput.addEventListener("change", function () {
      const minVal = Number.parseInt(this.value)
      const maxVal = Number.parseInt(maxPriceInput.value)

      if (minVal > maxVal) {
        this.value = maxVal
      }
    })

    // Ensure max price isn't less than min price
    maxPriceInput.addEventListener("change", function () {
      const maxVal = Number.parseInt(this.value)
      const minVal = Number.parseInt(minPriceInput.value)

      if (maxVal < minVal) {
        this.value = minVal
      }
    })
  }
})
