<?php
// Include necessary files
require_once '../config/database.php';
require_once '../models/Booking.php';
require_once '../models/Car.php';
require_once '../includes/session.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize objects
$booking = new Booking($db);
$car = new Car($db);

// Get all cars for filter dropdown
$cars = $car->getAllCars(0, 1000);

// Include header
include_once 'includes/header.php';
?>

<div class="admin-page-title">
    <h1>Booking Calendar</h1>
    <p class="text-muted">View bookings in calendar format</p>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="filter-container d-flex gap-3 flex-wrap">
            <div class="form-group">
                <label for="car-filter">Filter by Car</label>
                <select id="car-filter" class="form-select">
                    <option value="">All Cars</option>
                    <?php foreach ($cars as $car): ?>
                    <option value="<?php echo $car['id']; ?>">
                        <?php echo $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status-filter">Filter by Status</label>
                <select id="status-filter" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date-jump">Jump to Date</label>
                <input type="date" id="date-jump" class="form-control">
            </div>
        </div>
    </div>
    <div class="col-md-4 text-end">
        <a href="bookings.php" class="btn btn-secondary">
            <i class="fas fa-list me-1"></i> List View
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="booking-calendar"></div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="booking-details-modal" tabindex="-1" role="dialog" aria-labelledby="booking-details-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="booking-details-title">Booking Details</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="booking-details-content">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading booking details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <div>
                    <button type="button" class="btn btn-danger" id="cancel-booking-btn">Cancel Booking</button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary" id="view-booking-btn">View Details</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include JS files -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('booking-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 650,
            events: function(info, successCallback, failureCallback) {
                var carId = $('#car-filter').val();
                var status = $('#status-filter').val();
                
                $.ajax({
                    url: 'booking-calendar-data.php',
                    type: 'GET',
                    data: {
                        car_id: carId,
                        status: status
                    },
                    success: function(response) {
                        var events = [];
                        
                        try {
                            var data = JSON.parse(response);
                            
                            if (data.error) {
                                console.error('Error loading booking data:', data.error);
                                failureCallback(new Error(data.error));
                                return;
                            }
                            
                            data.forEach(function(booking) {
                                events.push({
                                    id: booking.id,
                                    title: booking.title,
                                    start: booking.start,
                                    end: booking.end,
                                    className: 'status-' + booking.status,
                                    extendedProps: {
                                        booking_id: booking.id,
                                        status: booking.status,
                                        user_name: booking.user_name,
                                        user_email: booking.user_email,
                                        car_name: booking.car_name,
                                        car_image: booking.car_image,
                                        pickup_location_name: booking.pickup_location_name,
                                        return_location_name: booking.return_location_name,
                                        total_price: booking.total_price
                                    }
                                });
                            });
                            
                            successCallback(events);
                        } catch (e) {
                            console.error('Error parsing booking data:', e);
                            failureCallback(e);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching booking data:', error);
                        failureCallback(error);
                    }
                });
            },
            eventClick: function(info) {
                var booking = info.event.extendedProps;
                
                // Show modal with booking details
                $('#booking-details-modal').modal('show');
                
                // Set up the view details button
                $('#view-booking-btn').attr('href', 'booking-view.php?id=' + booking.booking_id);
                
                // Show/hide cancel button based on status
                if (booking.status === 'cancelled' || booking.status === 'completed') {
                    $('#cancel-booking-btn').hide();
                } else {
                    $('#cancel-booking-btn').show();
                    
                    // Set up cancel booking button
                    $('#cancel-booking-btn').off('click').on('click', function() {
                        if (confirm('Are you sure you want to cancel this booking?')) {
                            $.ajax({
                                url: 'booking-details-ajax.php',
                                type: 'POST',
                                data: {
                                    booking_id: booking.booking_id,
                                    action: 'cancel'
                                },
                                success: function(response) {
                                    try {
                                        var data = JSON.parse(response);
                                        
                                        if (data.success) {
                                            alert('Booking cancelled successfully.');
                                            $('#booking-details-modal').modal('hide');
                                            calendar.refetchEvents();
                                        } else {
                                            alert('Error: ' + data.message);
                                        }
                                    } catch (e) {
                                        console.error('Error parsing response:', e);
                                        alert('An error occurred. Please try again.');
                                    }
                                },
                                error: function() {
                                    alert('An error occurred. Please try again.');
                                }
                            });
                        }
                    });
                }
                
                // Display booking details directly from the event data
                var statusClass = 'status-' + booking.status;
                var html = '<div class="badge bg-' + getStatusClass(booking.status) + ' mb-3">' + booking.status.toUpperCase() + '</div>';
                
                html += '<div class="booking-car-info d-flex align-items-center mb-3">';
                if (booking.car_image) {
                    html += '<div class="booking-car-img me-3" style="width:80px;height:60px;overflow:hidden;border-radius:4px;"><img src="../' + booking.car_image + '" alt="' + booking.car_name + '" style="width:100%;height:100%;object-fit:cover;"></div>';
                } else {
                    html += '<div class="booking-car-img me-3 bg-light d-flex align-items-center justify-content-center" style="width:80px;height:60px;border-radius:4px;"><i class="fas fa-car fa-2x text-muted"></i></div>';
                }
                html += '<div><h5 class="mb-0">' + booking.car_name + '</h5></div>';
                html += '</div>';
                
                html += '<div class="mb-2"><strong>Customer:</strong> ' + booking.user_name + '</div>';
                html += '<div class="mb-2"><strong>Email:</strong> ' + booking.user_email + '</div>';
                html += '<div class="mb-2"><strong>Pickup Date:</strong> ' + formatDate(info.event.start) + '</div>';
                html += '<div class="mb-2"><strong>Return Date:</strong> ' + formatDate(info.event.end) + '</div>';
                html += '<div class="mb-2"><strong>Pickup Location:</strong> ' + booking.pickup_location_name + '</div>';
                html += '<div class="mb-2"><strong>Return Location:</strong> ' + booking.return_location_name + '</div>';
                html += '<div class="mb-2"><strong>Total Price:</strong> $' + parseFloat(booking.total_price).toFixed(2) + '</div>';
                
                $('#booking-details-content').html(html);
            }
        });
        
        calendar.render();
        
        // Filter by car
        $('#car-filter').on('change', function() {
            calendar.refetchEvents();
        });
        
        // Filter by status
        $('#status-filter').on('change', function() {
            calendar.refetchEvents();
        });
        
        // Jump to date
        $('#date-jump').on('change', function() {
            var date = $(this).val();
            if (date) {
                calendar.gotoDate(date);
            }
        });
        
        // Format date for display
        function formatDate(date) {
            if (!date) return 'N/A';
            
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }
        
        // Get status class for badge
        function getStatusClass(status) {
            switch(status) {
                case 'confirmed': return 'success';
                case 'pending': return 'warning';
                case 'cancelled': return 'danger';
                case 'completed': return 'info';
                default: return 'secondary';
            }
        }
    });
</script>

<style>
    .fc-event {
        cursor: pointer;
    }
    
    .fc-event-title {
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .fc-daygrid-event {
        padding: 3px 5px;
    }
    
    .status-pending {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
    }
    
    .status-confirmed {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
    
    .status-completed {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }
    
    .status-cancelled {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
</style>

<?php include_once 'includes/footer.php'; ?>
