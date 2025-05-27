<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Baby Care Distribution</title>
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet">
        
        <!-- Bootstrap & Font Awesome -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            :root {
                --primary-color: #42C2FF;    /* Cyan blue */
                --secondary-color: #85F4FF;  /* Light cyan */
                --accent-color: #B8FFF9;     /* Very light cyan */
                --dark-color: #42C2FF;      /* Darker cyan */
            }

            body {
                font-family: 'Montserrat', sans-serif;
            }

            .navbar {
                padding: 1.5rem 0;
                transition: padding 0.3s ease;
            }

            .navbar-shrink {
                padding: 0.5rem 0;
                background-color: var(--primary-color) !important;
            }

            .navbar-brand {
                font-size: 1.5rem;
                font-weight: 700;
            }

            header.masthead {
                padding-top: 10.5rem;
                padding-bottom: 6rem;
                text-align: center;
                color: #fff;
                background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://source.unsplash.com/random/1920x1080/?baby');
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-position: center center;
                background-size: cover;
            }

            .masthead-heading {
                font-size: 3.25rem;
                font-weight: 700;
                line-height: 3.25rem;
                margin-bottom: 2rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            }

            .masthead-subheading {
                font-size: 1.5rem;
                font-style: italic;
                margin-bottom: 2rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            }

            .btn-xl {
                padding: 1.25rem 2.5rem;
                font-size: 1.125rem;
                font-weight: 700;
                text-transform: uppercase;
                border: none;
                border-radius: 50px;
                background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
                color: #000; /* Dark text for better contrast */
                transition: transform 0.3s ease;
            }

            .btn-xl:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            }

            .page-section {
                padding: 6rem 0;
            }

            .section-heading {
                font-size: 2.5rem;
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .section-subheading {
                font-size: 1rem;
                font-weight: 400;
                font-style: italic;
                margin-bottom: 4rem;
            }

            .service-icon {
                background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
                border-radius: 50%;
                width: 7rem;
                height: 7rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                transition: transform 0.3s ease;
            }

            .service-icon:hover {
                transform: scale(1.1);
            }

            .service-icon i {
                color: white;
                font-size: 2.5rem;
            }

            .footer {
                padding: 2rem 0;
                background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
                color: #000 !important;
            }

            .btn-social {
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.3s ease;
                background: #fff;
                color: var(--primary-color) !important;
            }

            .btn-social:hover {
                transform: translateY(-3px);
            }

            .text-gradient {
                background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .btn-primary {
                background-color: var(--primary-color) !important;
                border-color: var(--primary-color) !important;
                color: #000 !important; /* Dark text for better contrast */
            }

            .btn-primary:hover {
                background-color: var(--secondary-color) !important;
                border-color: var(--secondary-color) !important;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(66, 194, 255, 0.3);
            }

            /* Add some new styles for better contrast */
            .navbar-dark .navbar-nav .nav-link {
                color: #000 !important;
            }

            .footer a {
                color: #000 !important;
            }

            @media (max-width: 768px) {
                .masthead-heading {
                    font-size: 2rem;
                    line-height: 2rem;
                }
                
                .masthead-subheading {
                    font-size: 1.25rem;
                }

                .btn-xl {
                    padding: 1rem 2rem;
                    font-size: 1rem;
                }
            }
        </style>
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#page-top">
                    <i class="fas fa-baby text-gradient me-2"></i>
                    PT BMA
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item">
                                    <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                                </li>
                                <!-- @if (Route::has('register'))
                                    <li class="nav-item">
                                    <a href="{{ route('register') }}" class="nav-link">Register</a>
                                    </li>
                                @endif -->
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Masthead-->
        <header class="masthead">
            <div class="container">
                <div class="masthead-heading text-uppercase">Welcome to Baby Care Distribution</div>
                <div class="masthead-subheading">Your Trusted Partner for Baby Care Products</div>
                <a class="btn btn-primary btn-xl text-uppercase" href="#services">
                    <i class="fas fa-arrow-right me-2"></i>
                    Discover More
                </a>
            </div>
        </header>

        <!-- Services-->
        <section class="page-section" id="services">
            <div class="container">
                <div class="text-center">
                    <h2 class="section-heading text-uppercase">Our Services</h2>
                    <h3 class="section-subheading text-muted">What makes us your best choice</h3>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="service-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <h4 class="my-3">Quality Products</h4>
                            <p class="text-muted">We provide high-quality baby diapers and care products from trusted manufacturers worldwide.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="service-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4 class="my-3">Fast Delivery</h4>
                            <p class="text-muted">Quick and reliable delivery service to ensure your business runs smoothly.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="service-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h4 class="my-3">24/7 Support</h4>
                            <p class="text-muted">Our dedicated customer service team is always ready to assist you anytime.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer-->
        <footer class="footer py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 text-lg-start text-white">
                        <i class="fas fa-baby me-2"></i>
                        Copyright &copy; Baby Care 2025
                    </div>
                    <div class="col-lg-4 my-3 my-lg-0">
                        <a class="btn btn-light btn-social mx-2" href="#!">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="btn btn-light btn-social mx-2" href="#!">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="btn btn-light btn-social mx-2" href="#!">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a class="text-decoration-none me-3 text-white" href="#!">Privacy Policy</a>
                        <a class="text-decoration-none text-white" href="#!">Terms of Use</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom JS -->
        <script>
            // Navbar shrink function
            var navbarShrink = function () {
                const navbarCollapsible = document.body.querySelector('.navbar');
                if (!navbarCollapsible) {
                    return;
                }
                if (window.scrollY === 0) {
                    navbarCollapsible.classList.remove('navbar-shrink')
                } else {
                    navbarCollapsible.classList.add('navbar-shrink')
                }
            };

            // Shrink the navbar when page is scrolled
            document.addEventListener('scroll', navbarShrink);
            
            // Shrink the navbar when page is loaded
            navbarShrink();
        </script>
    </body>
</html>
