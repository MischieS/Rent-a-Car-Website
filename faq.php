<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Frequently Asked Questions - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <?php include('assets/includes/header.php') ?>
        <!-- /Header -->

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Frequently Asked Questions</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h6 class="section-subtitle">QUESTIONS & ANSWERS</h6>
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <p class="section-description">Find answers to common questions about our car rental services</p>
                </div>
                
                <div class="row mt-5">
                    <div class="col-lg-3 mb-4 mb-lg-0" data-aos="fade-up">
                        <div class="faq-tabs">
                            <div class="nav flex-column nav-pills" id="faq-tabs" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                    <i class="fas fa-info-circle"></i> General Questions
                                </button>
                                <button class="nav-link" id="reservations-tab" data-bs-toggle="pill" data-bs-target="#reservations" type="button" role="tab" aria-controls="reservations" aria-selected="false">
                                    <i class="fas fa-calendar-check"></i> Reservations
                                </button>
                                <button class="nav-link" id="payments-tab" data-bs-toggle="pill" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">
                                    <i class="fas fa-credit-card"></i> Payments & Fees
                                </button>
                                <button class="nav-link" id="policies-tab" data-bs-toggle="pill" data-bs-target="#policies" type="button" role="tab" aria-controls="policies" aria-selected="false">
                                    <i class="fas fa-shield-alt"></i> Policies & Insurance
                                </button>
                                <button class="nav-link" id="vehicle-tab" data-bs-toggle="pill" data-bs-target="#vehicle" type="button" role="tab" aria-controls="vehicle" aria-selected="false">
                                    <i class="fas fa-car"></i> Vehicle Information
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9" data-aos="fade-up" data-aos-delay="100">
                        <div class="tab-content" id="faq-tabContent">
                            <!-- General Questions -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="accordion" id="accordionGeneral">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                What documents do I need to rent a car?
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionGeneral">
                                            <div class="accordion-body">
                                                <p>To rent a car from Dreams Rent, you will need:</p>
                                                <ul>
                                                    <li>A valid driver's license (held for at least 1 year)</li>
                                                    <li>A valid credit card in the renter's name</li>
                                                    <li>Proof of identity (passport or national ID)</li>
                                                    <li>Proof of address (utility bill or bank statement)</li>
                                                </ul>
                                                <p>International customers may need to provide an International Driving Permit along with their national driver's license.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                What is the minimum age to rent a car?
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionGeneral">
                                            <div class="accordion-body">
                                                <p>The minimum age to rent a car from Dreams Rent is 21 years. However, drivers under 25 years may be subject to a young driver surcharge and may have restrictions on certain vehicle categories (luxury, premium, and specialty vehicles).</p>
                                                <p>Drivers must have held their license for at least one year prior to the rental date.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Can I add an additional driver to my rental?
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionGeneral">
                                            <div class="accordion-body">
                                                <p>Yes, you can add additional drivers to your rental. Each additional driver must meet the same requirements as the primary driver and must be present at the time of rental with their valid driver's license.</p>
                                                <p>There is a fee of $10 per day for each additional driver, with a maximum of 3 additional drivers per rental. Spouses or domestic partners may be exempt from this fee in some locations.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                What are your office hours?
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionGeneral">
                                            <div class="accordion-body">
                                                <p>Our standard office hours are:</p>
                                                <ul>
                                                    <li>Monday to Friday: 8:00 AM - 7:00 PM</li>
                                                    <li>Saturday: 9:00 AM - 5:00 PM</li>
                                                    <li>Sunday: 10:00 AM - 4:00 PM</li>
                                                </ul>
                                                <p>Airport locations may have extended hours. Please check the specific location's hours when making your reservation.</p>
                                                <p>After-hours pick-up and drop-off can be arranged for an additional fee with advance notice.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reservations -->
                            <div class="tab-pane fade" id="reservations" role="tabpanel" aria-labelledby="reservations-tab">
                                <div class="accordion" id="accordionReservations">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                                How do I make a reservation?
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#accordionReservations">
                                            <div class="accordion-body">
                                                <p>You can make a reservation in several ways:</p>
                                                <ul>
                                                    <li>Online through our website</li>
                                                    <li>By calling our reservation center at (123) 456-7890</li>
                                                    <li>By visiting any of our rental locations in person</li>
                                                    <li>Through our mobile app</li>
                                                </ul>
                                                <p>To complete your reservation, you'll need to provide your pickup and return dates/times, desired vehicle category, and contact information.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                                Can I modify or cancel my reservation?
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionReservations">
                                            <div class="accordion-body">
                                                <p>Yes, you can modify or cancel your reservation:</p>
                                                <ul>
                                                    <li>Modifications can be made online through your account, by calling our customer service, or by visiting a rental location.</li>
                                                    <li>Cancellations made at least 48 hours before the scheduled pickup time will receive a full refund.</li>
                                                    <li>Cancellations made less than 48 hours before pickup may be subject to a cancellation fee equivalent to one day's rental.</li>
                                                    <li>No-shows will be charged the full rental amount.</li>
                                                </ul>
                                                <p>Prepaid reservations may have different cancellation policies. Please check the terms and conditions of your specific reservation.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSeven">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                                Is a deposit required when making a reservation?
                                            </button>
                                        </h2>
                                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#accordionReservations">
                                            <div class="accordion-body">
                                                <p>For standard reservations, no deposit is required at the time of booking. However, a valid credit card is needed to guarantee your reservation.</p>
                                                <p>For prepaid reservations, full payment is required at the time of booking, which typically comes with a discount on the rental rate.</p>
                                                <p>At the time of pickup, a security deposit will be authorized on your credit card. The amount varies depending on the vehicle category and rental duration.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payments & Fees -->
                            <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                                <div class="accordion" id="accordionPayments">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingEight">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="true" aria-controls="collapseEight">
                                                What payment methods do you accept?
                                            </button>
                                        </h2>
                                        <div id="collapseEight" class="accordion-collapse collapse show" aria-labelledby="headingEight" data-bs-parent="#accordionPayments">
                                            <div class="accordion-body">
                                                <p>We accept the following payment methods:</p>
                                                <ul>
                                                    <li>Major credit cards (Visa, MasterCard, American Express, Discover)</li>
                                                    <li>Debit cards with Visa or MasterCard logo (additional restrictions may apply)</li>
                                                    <li>Digital wallets (Apple Pay, Google Pay) for online reservations</li>
                                                    <li>Corporate accounts with approved billing</li>
                                                </ul>
                                                <p>Cash, prepaid cards, and gift cards are not accepted as forms of payment or security deposit.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingNine">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                                Are there any additional fees I should be aware of?
                                            </button>
                                        </h2>
                                        <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#accordionPayments">
                                            <div class="accordion-body">
                                                <p>While we strive for transparent pricing, there are some additional fees that may apply:</p>
                                                <ul>
                                                    <li>Airport surcharge (for airport locations)</li>
                                                    <li>Additional driver fee ($10 per day per driver)</li>
                                                    <li>Young driver fee (for drivers aged 21-24)</li>
                                                    <li>Late return fee (hourly rate up to one full day)</li>
                                                    <li>Fuel service fee (if vehicle is not returned with the same fuel level)</li>
                                                    <li>Cleaning fee (for excessive dirt or odors)</li>
                                                    <li>Traffic and parking violations</li>
                                                    <li>Early return fee (may apply for prepaid rentals)</li>
                                                </ul>
                                                <p>All applicable fees will be disclosed at the time of reservation and rental agreement signing.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                                What is your fuel policy?
                                            </button>
                                        </h2>
                                        <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#accordionPayments">
                                            <div class="accordion-body">
                                                <p>We operate on a "full-to-full" fuel policy. This means:</p>
                                                <ul>
                                                    <li>Vehicles are provided with a full tank of fuel</li>
                                                    <li>You are expected to return the vehicle with a full tank</li>
                                                    <li>If the vehicle is not returned with a full tank, a refueling fee will be charged</li>
                                                </ul>
                                                <p>The refueling fee includes the cost of fuel plus a service charge. To avoid this fee, simply refill the tank at a gas station near the return location and provide the receipt.</p>
                                                <p>We also offer a Fuel Purchase Option (FPO) that allows you to prepay for a full tank of fuel at a competitive rate, eliminating the need to refill before returning.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Policies & Insurance -->
                            <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab">
                                <div class="accordion" id="accordionPolicies">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingEleven">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEleven" aria-expanded="true" aria-controls="collapseEleven">
                                                What insurance coverage options do you offer?
                                            </button>
                                        </h2>
                                        <div id="collapseEleven" class="accordion-collapse collapse show" aria-labelledby="headingEleven" data-bs-parent="#accordionPolicies">
                                            <div class="accordion-body">
                                                <p>We offer several insurance coverage options:</p>
                                                <ul>
                                                    <li><strong>Basic Coverage:</strong> Included in all rentals, provides liability coverage up to the minimum required by law.</li>
                                                    <li><strong>Collision Damage Waiver (CDW):</strong> Reduces your liability for damage to the rental vehicle.</li>
                                                    <li><strong>Supplemental Liability Insurance (SLI):</strong> Provides additional third-party liability coverage.</li>
                                                    <li><strong>Personal Accident Insurance (PAI):</strong> Covers medical expenses for you and your passengers.</li>
                                                    <li><strong>Personal Effects Coverage (PEC):</strong> Protects your personal belongings in the vehicle.</li>
                                                    <li><strong>Premium Protection Package:</strong> Combines CDW, SLI, PAI, and PEC for comprehensive coverage.</li>
                                                </ul>
                                                <p>Your personal auto insurance or credit card may provide rental car coverage. We recommend checking with your insurance provider or credit card company before purchasing additional coverage.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwelve">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                                                What is your smoking policy?
                                            </button>
                                        </h2>
                                        <div id="collapseTwelve" class="accordion-collapse collapse" aria-labelledby="headingTwelve" data-bs-parent="#accordionPolicies">
                                            <div class="accordion-body">
                                                <p>All our vehicles are non-smoking. Smoking (including e-cigarettes and vaping) is strictly prohibited in all rental vehicles.</p>
                                                <p>If evidence of smoking is found in the vehicle, a cleaning fee of up to $250 will be charged to cover the cost of special cleaning required to remove smoke odors and residue.</p>
                                                <p>This policy is in place to ensure the comfort of all our customers and to maintain the quality of our vehicles.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThirteen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThirteen" aria-expanded="false" aria-controls="collapseThirteen">
                                                What should I do in case of an accident or breakdown?
                                            </button>
                                        </h2>
                                        <div id="collapseThirteen" class="accordion-collapse collapse" aria-labelledby="headingThirteen" data-bs-parent="#accordionPolicies">
                                            <div class="accordion-body">
                                                <p>In case of an accident:</p>
                                                <ol>
                                                    <li>Ensure the safety of all passengers and seek medical attention if needed</li>
                                                    <li>Contact local authorities and obtain a police report</li>
                                                    <li>Do not admit fault or liability</li>
                                                    <li>Call our emergency assistance number at (123) 456-7890</li>
                                                    <li>Document the scene with photos and collect information from other parties involved</li>
                                                    <li>Complete an accident report form (available in the glove compartment)</li>
                                                </ol>
                                                <p>In case of a breakdown:</p>
                                                <ol>
                                                    <li>Move the vehicle to a safe location if possible</li>
                                                    <li>Call our 24/7 roadside assistance at (123) 456-7890</li>
                                                    <li>Provide your location, rental agreement number, and description of the issue</li>
                                                    <li>Wait for assistance to arrive</li>
                                                </ol>
                                                <p>All our vehicles come with complimentary 24/7 roadside assistance for your peace of mind.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vehicle Information -->
                            <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                                <div class="accordion" id="accordionVehicle">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFourteen">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFourteen" aria-expanded="true" aria-controls="collapseFourteen">
                                                What types of vehicles do you offer?
                                            </button>
                                        </h2>
                                        <div id="collapseFourteen" class="accordion-collapse collapse show" aria-labelledby="headingFourteen" data-bs-parent="#accordionVehicle">
                                            <div class="accordion-body">
                                                <p>We offer a wide range of vehicles to suit every need and budget:</p>
                                                <ul>
                                                    <li><strong>Economy:</strong> Compact cars ideal for city driving and fuel efficiency</li>
                                                    <li><strong>Sedan:</strong> Mid-size and full-size cars with more space and comfort</li>
                                                    <li><strong>SUV:</strong> Small, medium, and large SUVs for more space and versatility</li>
                                                    <li><strong>Luxury:</strong> Premium vehicles with advanced features and superior comfort</li>
                                                    <li><strong>Convertible:</strong> Open-top vehicles for an enhanced driving experience</li>
                                                    <li><strong>Van/Minivan:</strong> Spacious vehicles for family trips or group travel</li>
                                                    <li><strong>Specialty:</strong> Sports cars and high-performance vehicles</li>
                                                </ul>
                                                <p>All our vehicles are late models (less than 2 years old) and undergo regular maintenance to ensure reliability and safety.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFifteen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFifteen" aria-expanded="false" aria-controls="collapseFifteen">
                                                Can I request specific features or models?
                                            </button>
                                        </h2>
                                        <div id="collapseFifteen" class="accordion-collapse collapse" aria-labelledby="headingFifteen" data-bs-parent="#accordionVehicle">
                                            <div class="accordion-body">
                                                <p>When making a reservation, you select a vehicle category rather than a specific make or model. We guarantee you'll receive a vehicle in the category you reserved or higher.</p>
                                                <p>While we cannot guarantee specific makes or models, you can request certain features or preferences:</p>
                                                <ul>
                                                    <li>Automatic or manual transmission</li>
                                                    <li>GPS navigation system</li>
                                                    <li>Child safety seats</li>
                                                    <li>Bluetooth connectivity</li>
                                                    <li>Roof racks or ski racks</li>
                                                </ul>
                                                <p>For premium and luxury categories, you can request specific models for an additional fee, subject to availability.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSixteen">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSixteen" aria-expanded="false" aria-controls="collapseSixteen">
                                                What additional equipment can I rent?
                                            </button>
                                        </h2>
                                        <div id="collapseSixteen" class="accordion-collapse collapse" aria-labelledby="headingSixteen" data-bs-parent="#accordionVehicle">
                                            <div class="accordion-body">
                                                <p>We offer various additional equipment to enhance your rental experience:</p>
                                                <ul>
                                                    <li><strong>GPS Navigation:</strong> $10 per day</li>
                                                    <li><strong>Child Safety Seats:</strong>
                                                        <ul>
                                                            <li>Infant seat (0-12 months): $10 per day</li>
                                                            <li>Toddler seat (1-3 years): $10 per day</li>
                                                            <li>Booster seat (4-8 years): $7 per day</li>
                                                        </ul>
                                                    </li>
                                                    <li><strong>Roof Racks:</strong> $15 per day</li>
                                                    <li><strong>Ski/Snowboard Racks:</strong> $15 per day</li>
                                                    <li><strong>Bike Racks:</strong> $15 per day</li>
                                                    <li><strong>Mobile WiFi Hotspot:</strong> $12 per day</li>
                                                    <li><strong>Toll Pass Transponder:</strong> $5 per day plus tolls</li>
                                                </ul>
                                                <p>All equipment is subject to availability and should be requested at the time of reservation to ensure it's ready for your pickup.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="faq-contact-box mt-5" data-aos="fade-up">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <h3>Couldn't Find Your Answer?</h3>
                            <p>If you couldn't find the answer to your question, please contact our customer support team.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="contact.php" class="btn btn-primary">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /FAQ Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    });
    </script>
</body>
</html>
