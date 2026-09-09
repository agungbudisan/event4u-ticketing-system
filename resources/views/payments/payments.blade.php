<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event4U - Ticket Booking</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            color: #000;
        }
        header {
            background-color: #880e1f;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: #fff;
            margin-left: 1rem;
            text-decoration: none;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            padding: 2rem;
            gap: 2rem;
        }
        .card {
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 8px;
        }
        .event-image {
            width: 250px;
        }
        .form-section {
            flex: 1;
            min-width: 300px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
        }
        .radio-group {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .order-summary {
            margin-top: 1rem;
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 8px;
        }
        .order-header {
            background: yellow;
            text-align: center;
            padding: 0.5rem;
            font-weight: bold;
        }
        .order-details {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        .email-notif {
            margin-top: 1rem;
        }
        .btn-pay {
            margin-top: 1rem;
            background-color: #880e1f;
            color: #fff;
            padding: 0.7rem 2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .payment-section {
            padding: 2rem;
        }
        .accordion {
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        footer {
            background-color: #880e1f;
            color: #fff;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        footer div {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<header>
    <div>Event4U</div>
    <nav>
        <a href="#">Home</a>
        <a href="#">Category</a>
        <a href="#">Login</a>
        <a href="#">Sign Up</a>
        <a href="#">Contact</a>
    </nav>
</header>

<main class="container">
    <div class="card">
        <img src="https://via.placeholder.com/250x400?text=Billie+Eilish+Poster" alt="Billie Eilish Poster" class="event-image">
        <h3>Konser Billie Eilish</h3>
        <p><strong>Tanggal:</strong> 23 Oktober - 27 Oktober 2025</p>
        <p><strong>Waktu:</strong> 19:00 - 22:00</p>
        <p><strong>Lokasi:</strong> Stadion Manjá</p>
    </div>

    <div class="form-section">
        <h3>Attendee</h3>
        <form>
            <div class="form-group">
                <label>Name*</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email*</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Date of Birth*</label>
                <input type="date" name="dob" required>
            </div>
            <div class="form-group">
                <label>Phone Number*</label>
                <input type="tel" name="phone" required>
            </div>
            <div class="form-group">
                <label>ID Number (KTP/Driving License/Passport, etc)*</label>
                <input type="text" name="id_number" required>
            </div>
            <div class="form-group">
                <label>Gender*</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male"> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                </div>
            </div>

            <div class="order-summary">
                <div class="order-header">
                    23.59 Remaining booking Time
                </div>
                <div class="order-details">
                    <div>
                        <p>Pre-Sale</p>
                        <p>Admin Fee</p>
                        <p>Local Tax</p>
                    </div>
                    <div style="text-align: right;">
                        <p>Rp 900.000</p>
                        <p>Rp 6.000</p>
                        <p>Rp 25.000</p>
                    </div>
                </div>
                <hr>
                <div class="order-details" style="font-weight: bold;">
                    <div>Total</div>
                    <div>Rp 931.000</div>
                </div>
                <div class="email-notif">
                    <label>
                        <input type="checkbox" name="notif"> I agree to receive ticket booking notifications via Email.
                    </label>
                </div>
                <button type="submit" class="btn-pay">Pay Now</button>
            </div>
        </form>
    </div>
</main>

<section class="payment-section">
    <h3>Payment Method</h3>
    <div class="accordion">
        <strong>Credit Card</strong>
    </div>
    <div class="accordion">
        <strong>Virtual Account</strong>
    </div>
    <div class="accordion">
        <strong>Wallet</strong>
    </div>
    <div class="accordion">
        <strong>PayLater</strong>
    </div>
</section>

<footer>
    <div>
        <h4>OUR ADDRESS</h4>
        <p>57125 Surakarta<br>PT Tiket Indonesia<br>Cedung Selatan Lantai 8</p>
    </div>
    <div>
        <h4>OUR CONTACT</h4>
        <p>pntontngkt@tik.in<br>+62 123456789</p>
    </div>
</footer>

</body>
</html>
