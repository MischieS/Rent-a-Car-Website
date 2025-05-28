document.addEventListener("DOMContentLoaded", () => {
  // Global variables
  const currentDate = new Date()
  let selectedPickupDate = null
  let selectedReturnDate = null
  let unavailableDates = []
  let pickupCalendar, returnCalendar
  const bootstrap = window.bootstrap // Declare the bootstrap variable
  let currentMonth = new Date().getMonth() // New variable for current month
  let currentYear = new Date().getFullYear() // New variable for current year

  // Function to initialize the booking calendar
  function initBookingCalendar(carId) {
    console.log("Initializing booking calendar for car ID:", carId)

    // Reset any previous errors
    document.getElementById("calendarError").style.display = "none"

    // Show the calendar modal
    const calendarModal = new bootstrap.Modal(document.getElementById("calendarModal"))
    calendarModal.show()

    // Fetch unavailable dates for this car
    fetchUnavailableDates(carId)
  }

  // Function to fetch unavailable dates from the server
  function fetchUnavailableDates(carId) {
    // Show loading indicator
    document.getElementById("calendarLoading").style.display = "block"
    document.getElementById("calendarContent").style.display = "none"
    document.getElementById("calendarError").style.display = "none"

    console.log("Fetching unavailable dates for car ID:", carId)

    // Get the current URL path to determine the correct path to the PHP file
    const currentPath = window.location.pathname
    const pathToRoot = currentPath.includes("/admin/") ? "../" : ""
    const apiUrl = `${pathToRoot}get-unavailable-dates.php?car_id=${carId}`

    console.log("API URL:", apiUrl)

    // Fetch data from server
    fetch(apiUrl)
      .then((response) => {
        console.log("Response status:", response.status)
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`)
        }
        return response.json()
      })
      .then((data) => {
        console.log("Data received:", data)
        if (data.success) {
          unavailableDates = data.unavailable_dates || []
          console.log("Unavailable dates:", unavailableDates)

          // Initialize the calendar
          initializeCalendar(unavailableDates)

          // Hide loading indicator
          document.getElementById("calendarLoading").style.display = "none"
          document.getElementById("calendarContent").style.display = "block"
        } else {
          console.error("Error fetching unavailable dates:", data.message)
          // Show error message
          document.getElementById("calendarError").textContent = "Error loading availability data. Please try again."
          document.getElementById("calendarError").style.display = "block"
          document.getElementById("calendarLoading").style.display = "none"
        }
      })
      .catch((error) => {
        console.error("Fetch error:", error)
        // Show error message
        document.getElementById("calendarError").textContent = "Error loading availability data. Please try again."
        document.getElementById("calendarError").style.display = "block"
        document.getElementById("calendarLoading").style.display = "none"
      })
  }

  // Initialize calendar with unavailable dates
  function initializeCalendar(dates) {
    // Make sure dates is an array
    const unavailableDates = Array.isArray(dates) ? dates : []

    // Reset selected dates
    selectedPickupDate = null
    selectedReturnDate = null

    // Update display
    document.getElementById("pickupDateDisplay").textContent = "Select Date"
    document.getElementById("pickupDateDisplay").removeAttribute("data-date")
    document.getElementById("returnDateDisplay").textContent = "Select Date"
    document.getElementById("returnDateDisplay").removeAttribute("data-date")
    document.getElementById("continueBooking").disabled = true

    // Render both calendars
    renderCalendar(currentMonth, currentYear, "calendar1", unavailableDates)
    renderCalendar(
      (currentMonth + 1) % 12,
      currentMonth + 1 > 11 ? currentYear + 1 : currentYear,
      "calendar2",
      unavailableDates,
    )

    // Set up navigation buttons
    document.getElementById("prevMonth").onclick = () => {
      currentMonth--
      if (currentMonth < 0) {
        currentMonth = 11
        currentYear--
      }
      renderCalendar(currentMonth, currentYear, "calendar1", unavailableDates)
      renderCalendar(
        (currentMonth + 1) % 12,
        currentMonth + 1 > 11 ? currentYear + 1 : currentYear,
        "calendar2",
        unavailableDates,
      )
    }

    document.getElementById("nextMonth").onclick = () => {
      currentMonth++
      if (currentMonth > 11) {
        currentMonth = 0
        currentYear++
      }
      renderCalendar(currentMonth, currentYear, "calendar1", unavailableDates)
      renderCalendar(
        (currentMonth + 1) % 12,
        currentMonth + 1 > 11 ? currentYear + 1 : currentYear,
        "calendar2",
        unavailableDates,
      )
    }
  }

  // Function to render a calendar
  function renderCalendar(month, year, calendarId, unavailableDates) {
    const calendarElement = document.getElementById(calendarId)
    if (!calendarElement) return

    // Make sure unavailableDates is an array
    unavailableDates = Array.isArray(unavailableDates) ? unavailableDates : []

    // Create month names array
    const monthNames = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ]

    // Create day names array
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay()
    const daysInMonth = new Date(year, month + 1, 0).getDate()

    // Create calendar header
    let calendarHTML = `
          <div class="calendar-header">${monthNames[month]} ${year}</div>
          <table class="calendar-table">
              <thead>
                  <tr>
      `

    // Add day names
    for (let i = 0; i < 7; i++) {
      calendarHTML += `<th>${dayNames[i]}</th>`
    }

    calendarHTML += `
                  </tr>
              </thead>
              <tbody>
                  <tr>
      `

    // Add empty cells for days before first day of month
    for (let i = 0; i < firstDay; i++) {
      calendarHTML += `<td class="empty"></td>`
    }

    // Add days of month
    let dayCount = 1
    for (let i = firstDay; i < 7; i++) {
      calendarHTML += getCellHTML(dayCount, month, year, unavailableDates)
      dayCount++
    }

    calendarHTML += `</tr>`

    // Add remaining days
    while (dayCount <= daysInMonth) {
      calendarHTML += `<tr>`

      for (let i = 0; i < 7; i++) {
        if (dayCount <= daysInMonth) {
          calendarHTML += getCellHTML(dayCount, month, year, unavailableDates)
        } else {
          calendarHTML += `<td class="empty"></td>`
        }
        dayCount++
      }

      calendarHTML += `</tr>`
    }

    calendarHTML += `
              </tbody>
          </table>
      `

    // Set calendar HTML
    calendarElement.innerHTML = calendarHTML

    // Add event listeners to date cells
    const dateCells = calendarElement.querySelectorAll("td.available")
    dateCells.forEach((cell) => {
      cell.addEventListener("click", function () {
        const day = Number.parseInt(this.textContent)
        const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`

        // Handle date selection
        if (!selectedPickupDate || (selectedPickupDate && selectedReturnDate)) {
          // Start new selection
          selectedPickupDate = dateStr
          selectedReturnDate = null
          updateDateDisplay()
          highlightSelectedDates(unavailableDates)
        } else {
          // Complete selection
          if (dateStr < selectedPickupDate) {
            selectedReturnDate = selectedPickupDate
            selectedPickupDate = dateStr
          } else {
            selectedReturnDate = dateStr
          }
          updateDateDisplay()
          highlightSelectedDates(unavailableDates)

          // Enable continue button if both dates are selected
          document.getElementById("continueBooking").disabled = false
        }
      })
    })
  }

  // Function to get HTML for a calendar cell
  function getCellHTML(day, month, year, unavailableDates) {
    // Make sure unavailableDates is an array
    unavailableDates = Array.isArray(unavailableDates) ? unavailableDates : []

    const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const cellDate = new Date(year, month, day)

    // Check if date is in the past
    if (cellDate < today) {
      return `<td class="disabled">${day}</td>`
    }

    // Check if date is unavailable
    if (unavailableDates.includes(dateStr)) {
      return `<td class="unavailable">${day}</td>`
    }

    // Check if date is today
    const isToday = cellDate.getTime() === today.getTime()

    // Check if date is selected
    const isSelectedStart = selectedPickupDate === dateStr
    const isSelectedEnd = selectedReturnDate === dateStr

    const isInRange =
      selectedPickupDate && selectedReturnDate && dateStr > selectedPickupDate && dateStr < selectedReturnDate

    // Build class string
    let classString = "available"
    if (isToday) classString += " today"
    if (isSelectedStart) classString += " selected pickup"
    if (isSelectedEnd) classString += " selected return"
    if (isInRange) classString += " in-range"

    return `<td class="${classString}">${day}</td>`
  }

  // Function to update date display
  function updateDateDisplay() {
    const pickupDateDisplay = document.getElementById("pickupDateDisplay")
    const returnDateDisplay = document.getElementById("returnDateDisplay")

    if (selectedPickupDate) {
      const formattedStartDate = formatDisplayDate(selectedPickupDate)
      pickupDateDisplay.textContent = formattedStartDate
      pickupDateDisplay.setAttribute("data-date", selectedPickupDate)
    } else {
      pickupDateDisplay.textContent = "Select Date"
      pickupDateDisplay.removeAttribute("data-date")
    }

    if (selectedReturnDate) {
      const formattedEndDate = formatDisplayDate(selectedReturnDate)
      returnDateDisplay.textContent = formattedEndDate
      returnDateDisplay.setAttribute("data-date", selectedReturnDate)
    } else {
      returnDateDisplay.textContent = "Select Date"
      returnDateDisplay.removeAttribute("data-date")
    }
  }

  // Function to highlight selected dates
  function highlightSelectedDates(unavailableDates) {
    // Re-render both calendars to update highlighting
    renderCalendar(currentMonth, currentYear, "calendar1", unavailableDates)
    renderCalendar(
      (currentMonth + 1) % 12,
      currentMonth + 1 > 11 ? currentYear + 1 : currentYear,
      "calendar2",
      unavailableDates,
    )
  }

  // Format date for display
  function formatDisplayDate(dateStr) {
    const date = new Date(dateStr)
    const options = { weekday: "short", month: "short", day: "numeric", year: "numeric" }
    return date.toLocaleDateString("en-US", options)
  }

  // Function to continue to booking form
  function continueToBookingForm() {
    if (selectedPickupDate && selectedReturnDate) {
      const carId = localStorage.getItem("selectedCarId")
      window.location.href = `booking-form.php?car_id=${carId}&pickup_date=${selectedPickupDate}&return_date=${selectedReturnDate}`
    }
  }

  // Add event listener to continue button
  document.getElementById("continueBooking")?.addEventListener("click", continueToBookingForm)

  // Add event listeners to all "Book Now" buttons
  document.querySelectorAll(".book-now-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault()
      const carId = this.dataset.carId
      console.log("Book Now clicked for car ID:", carId)
      initBookingCalendar(carId)
    })
  })

  // Make initializeCalendar function available globally
  window.initializeCalendar = initializeCalendar
})
