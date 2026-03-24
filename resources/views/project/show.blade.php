<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name ?? 'Ultra Residences' }} | Luxury Living</title>
    <meta name="description" content="{{ $project->short_description ?? 'Experience the pinnacle of luxury with bespoke amenities and timeless elegance.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root 
        {
            --primary: {{ $styleSettings['primary_color'] ?? '#1a1a1a' }};
            --secondary: {{ $styleSettings['secondary_color'] ?? '#d4a373' }};
            --accent: {{ $styleSettings['accent_color'] ?? '#4a5e6a' }};
            --text: {{ $styleSettings['text_color'] ?? '#333333' }};
            --text-light: {{ $styleSettings['text_secondary'] ?? '#666666' }};
            --border-radius: {{ isset($styleSettings['card_radius']) ? $styleSettings['card_radius'] : '12px' }};
            --font-heading: {{ isset($styleSettings['font_heading']) ? "'" . $styleSettings['font_heading'] . "', serif" : "'Playfair Display', serif" }};
            --font-body: {{ isset($styleSettings['font_body']) ? "'" . $styleSettings['font_body'] . "', sans-serif" : "'Inter', sans-serif" }};
            --gold-gradient: linear-gradient(135deg, {{ $styleSettings['accent_color'] ?? '#4a5e6a' }}, #f2c894);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 15px 35px rgba(0, 0, 0, 0.15);
            --light: #f8f8f8;
            --white: #ffffff;
            --section-spacing: 80px;
            --header-height: 90px;
            --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --line-height-body: 1.8;
            --line-height-heading: 1.3;
        }

        {{ $styleSettings['custom_css'] ?? '' }}

        *,
        *::before,
        *::after 
        {
            box-sizing: border-box;
        }

        html 
        {
            scroll-behavior: smooth;
        }

        body 
        {
            font-family: {{ $styleSettings['font_body'] }};
            color: var(--text);
            background: var(--light);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            line-height: var(--line-height-body);
        }

        h1, h2, h3, h4, h5, h6 
        {
            font-family: {{ $styleSettings['font_heading'] }};
            font-weight: 600;
            line-height: var(--line-height-heading);
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }

        p 
        {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        a 
        {
            text-decoration: none;
            transition: var(--transition);
        }

        img 
        {
            max-width: 100%;
            height: auto;
            display: block;
        }

        h1 { font-size: clamp(2.5rem, 5vw, 4.5rem); }
        h2 { font-size: clamp(2rem, 4vw, 3rem); }
        h3 { font-size: clamp(1.75rem, 3.5vw, 2.25rem); }
        h4 { font-size: clamp(1.5rem, 3vw, 1.75rem); }
        h5 { font-size: clamp(1.25rem, 2.5vw, 1.5rem); }
        h6 { font-size: clamp(1rem, 2vw, 1.25rem); }

        .btn 
        {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.875rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
            padding: 1rem 2.25rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary 
        {
            background: var(--gold-gradient);
            color: var(--primary);
            font-weight: 600;
        }

        .btn-primary:hover 
        {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            color: var(--primary);
        }

        .btn-outline 
        {
            background: transparent;
            border: 2px solid var(--white);
            color: var(--white);
            font-weight: 600;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-outline:hover 
        {
            background: var(--white);
            color: var(--primary);
            transform: translateY(-3px);
        }

        .container 
        {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .section 
        {
            padding: var(--section-spacing) 0;
            position: relative;
        }

        .section-title 
        {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title h2 
        {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .section-title h2::after 
        {
            content: '';
            position: absolute;
            bottom: -1rem;
            left: 50%;
            transform: translateX(-50%);
            width: 5rem;
            height: 0.25rem;
            background: var(--gold-gradient);
            border-radius: 2px;
        }

        .section-title p 
        {
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
            font-size: 1.25rem;
        }

        .navbar 
        {
            padding: 1.5rem 0;
            transition: var(--transition);
            background: transparent !important;
        }

        .navbar.scrolled 
        {
            background: rgba(26, 26, 26, 0.98) !important;
            padding: 1rem 0;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar-brand 
        {
            font-family: {{ $styleSettings['font_heading'] }};
            font-size: 1.75rem;
            color: var(--white) !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
        }

        .navbar-brand img 
        {
            height: 2.625rem;
            transition: var(--transition);
        }

        .navbar.scrolled .navbar-brand img 
        {
            height: 2.25rem;
        }

        .nav-link 
        {
            color: var(--white) !important;
            font-size: 0.9375rem;
            font-weight: 500;
            text-transform: uppercase;
            padding: 0.5rem 0 !important;
            margin: 0 1rem;
            position: relative;
            letter-spacing: 1px;
        }

        .nav-link::after 
        {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: var(--transition);
        }

        .nav-link:hover::after 
        {
            width: 100%;
        }

        .navbar-toggler 
        {
            border: none;
            padding: 0.75rem;
        }

        .navbar-toggler:focus 
        {
            box-shadow: none;
        }

        .navbar-toggler-icon 
        {
            background-image: none;
            width: 1.75rem;
            height: 2px;
            position: relative;
            transition: var(--transition);
        }

        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after 
        {
            content: '';
            position: absolute;
            width: 1.75rem;
            height: 2px;
            background: var(--white);
            transition: var(--transition);
        }

        .navbar-toggler-icon::before {
            transform: translateY(-0.5rem);
        }

        .navbar-toggler-icon::after {
            transform: translateY(0.5rem);
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            background-color: transparent;
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::before {
            transform: rotate(45deg);
        }

        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::after {
            transform: rotate(-45deg);
        }

        .hero {
            height: 100vh;
            min-height: 45rem;
            position: relative;
            display: flex;
            align-items: center;
            background: var(--primary);
            overflow: hidden;
        }

        .hero-banner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease, transform 8s cubic-bezier(0.25, 0.45, 0.45, 0.95);
            position: absolute;
            top: 0;
            left: 0;
        }

        .hero-banner img.active {
            opacity: 1;
            z-index: 1;
        }

        .hero-banner img.next {
            opacity: 0;
            z-index: 0;
        }

        .hero:hover .hero-banner img {
            transform: scale(1.05);
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, 
                rgba(0, 0, 0, 0.7) 0%, 
                rgba(0, 0, 0, 0.5) 30%, 
                rgba(0, 0, 0, 0.3) 60%, 
                rgba(0, 0, 0, 0.1) 100%);
            z-index: 1;
        }

        .hero-content {
            max-width: 56.25rem;
            color: var(--white);
            text-align: center;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            padding: 0 1.25rem;
        }

        .hero-content h1 {
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            font-weight: 700;
            letter-spacing: 1px;
        }

        .hero-content p {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .price-badge {
            display: inline-flex;
            background: var(--gold-gradient);
            color: var(--primary);
            padding: 1rem 2.5rem;
            border-radius: var(--border-radius);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            letter-spacing: 1px;
            flex-direction: column;
            line-height: 1.3;
        }
        .btn-cust
        {
            background: var(--gold-gradient);
            color: var(--primary); 
            font-weight:bold;
        }

        .hero-cta {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
            background: var(--white);
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .card:hover {
            transform: translateY(-0.625rem);
            box-shadow: var(--shadow-hover);
        }

        .card-body {
            padding: 2.5rem;
        }

        .highlight-card {
            text-align: center;
        }

        .highlight-card h3 {
            font-size: 2.25rem;
            color: var(--secondary);
            margin-bottom: 0.625rem;
        }

        .highlight-card p {
            font-size: 1rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .feature-card, .amenity-card {
            text-align: center;
        }

        .feature-icon, .amenity-icon {
            width: 5rem;
            height: 5rem;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.875rem;
            margin: 0 auto 1.5rem;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon,
        .amenity-card:hover .amenity-icon {
            transform: scale(1.1);
        }

        .feature-card h4, .amenity-card h4 {
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .video-container {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            aspect-ratio: 16/9;
            margin-bottom: 1.875rem;
            transform: perspective(1000px) rotateY(15deg);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .video-container:hover {
            transform: perspective(1000px) rotateY(-5deg) translateY(-0.625rem);
            box-shadow: var(--shadow-hover);
        }

        .video-container:hover .video-thumbnail {
            transform: scale(1.05);
        }

        .video-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 5rem;
            height: 5rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            z-index: 2;
        }

        .play-button:hover {
            transform: translate(-50%, -50%) scale(1.1);
            background: var(--white);
        }

        .project-description {
            margin-top: 3rem;
        }

        .project-description h3 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .project-description .lead {
            font-size: 1.25rem;
            color: var(--text);
        }

        .spec-item {
            transition: var(--transition);
        }

        .spec-icon {
            width: 3rem;
            height: 3rem;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
        }

        .gallery-item {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            aspect-ratio: 1/1;
        }

        .gallery-item:hover {
            transform: translateY(-0.625rem);
            box-shadow: var(--shadow-hover);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay i {
            color: var(--white);
            font-size: 2.5rem;
            transform: scale(0.8);
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay i {
            transform: scale(1);
        }

        .location-map {
            height: 37.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .location-map iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .location-address, .landmark-item {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.875rem;
            transition: var(--transition);
        }

        .location-address:hover, .landmark-item:hover {
            transform: translateY(-0.3125rem);
            box-shadow: var(--shadow-hover);
        }

        .location-icon, .landmark-icon {
            width: 3.125rem;
            height: 3.125rem;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .landmark-item h5 {
            margin-bottom: 0.5rem;
            color: var(--primary);
        }

        .footer {
            background: var(--primary);
            color: var(--white);
            padding: 6.25rem 0 1.875rem;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 9.375rem;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), transparent);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3.125rem;
            margin-bottom: 3.75rem;
        }

        .footer-logo {
            font-size: 2rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--white);
            font-weight: 700;
        }

        .footer-logo img {
            height: 3rem;
        }

        .footer-about p {
            font-size: 1rem;
            max-width: 25rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.875rem;
        }

        .social-links a {
            width: 3.125rem;
            height: 3.125rem;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.125rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            transform: translateY(-0.3125rem);
            box-shadow: var(--shadow);
        }

        .footer-links h4 {
            margin-bottom: 1.5rem;
            color: var(--white);
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.9375rem;
        }

        .footer-links a {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--secondary);
            transform: translateX(0.3125rem);
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.9375rem;
        }

        .contact-icon {
            width: 3.125rem;
            height: 3.125rem;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .contact-details h5 {
            margin-bottom: 0.5rem;
            color: var(--white);
        }

        .contact-details a, .contact-details p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0;
        }

        .contact-details a:hover {
            color: var(--secondary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            font-size: 0.9375rem;
            opacity: 0.7;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0;
        }

        .modal {
            background: rgba(0, 0, 0, 0.95);
            z-index: 99999;
        }

        .modal-content {
            background: transparent;
            border: none;
            position: relative;
        }

        .modal-body {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .modal-body img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .btn-close {
            position: fixed;
            top: 2rem;
            right: 2rem;
            color: var(--white);
            font-size: 2.25rem;
            opacity: 1;
            background: none;
            transition: var(--transition);
            z-index: 2001;
            width: auto;
            height: auto;
            padding: 0.5rem;
            margin: 0;
        }

        .btn-close:hover {
            color: var(--secondary);
            transform: rotate(90deg);
        }

        .modal-nav-btn {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            width: 3.75rem;
            height: 3.75rem;
            background: var(--gold-gradient);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            z-index: 2001;
            opacity: 0.8;
            border: none;
        }

        .modal-nav-btn:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .modal-nav-btn.prev {
            left: 2rem;
        }

        .modal-nav-btn.next {
            right: 2rem;
        }

        .whatsapp-float {
            position: fixed;
            bottom: 2.5rem;
            right: 2.5rem;
            width: 4.375rem;
            height: 4.375rem;
            background: #25D366;
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: var(--shadow);
            z-index: 100;
            transition: var(--transition);
            animation: pulse 2s infinite;
            text-decoration: none;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            animation: none;
            color: var(--white);
        }

        .back-to-top {
            position: fixed;
            bottom: 2.5rem;
            right: 7.5rem;
            width: 3.75rem;
            height: 3.75rem;
            background: var(--gold-gradient);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: var(--shadow);
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            text-decoration: none;
        }

        .back-to-top.active {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-0.3125rem);
            color: var(--primary);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 1.25rem rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 1s ease forwards;
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(1.25rem);
            animation: fadeInUp 0.8s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(1.25rem); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mb-6 {
            margin-bottom: 3.75rem;
        }

        .mt-6 {
            margin-top: 3.75rem;
        }

        @media (max-width: 1200px) {
            .section {
                padding: 6.25rem 0;
            }
        }

        @media (max-width: 992px) {
            .hero {
                min-height: 40rem;
            }
            
            .hero-content h1 {
                margin-bottom: 1.25rem;
            }
            
            .hero-content p {
                font-size: 1.125rem;
                margin-bottom: 2rem;
            }
            
            .price-badge {
                font-size: 1.25rem;
                margin-bottom: 2rem;
            }
            
            .section-title {
                margin-bottom: 3.75rem;
            }
            
            .section-title p {
                font-size: 1.125rem;
            }
        }

        @media (max-width: 768px) {
            .hero {
                min-height: 35rem;
            }
            
            .hero-content h1 {
                margin-bottom: 1rem;
            }
            
            .price-badge {
                font-size: 1.125rem;
                padding: 0.75rem 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }
            
            .whatsapp-float {
                width: 3.75rem;
                height: 3.75rem;
                font-size: 1.75rem;
                bottom: 1.5rem;
                right: 1.5rem;
            }
            
            .back-to-top {
                width: 3.125rem;
                height: 3.125rem;
                font-size: 1.125rem;
                right: 6.25rem;
                bottom: 1.5rem;
            }
            
            .location-map {
                height: 25rem;
            }
        }

        @media (max-width: 576px) {
            .hero {
                min-height: 30rem;
            }
            
            .hero-content h1 {
                margin-bottom: 0.75rem;
            }
            
            .hero-content p {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .price-badge {
                font-size: 1rem;
                padding: 0.75rem 1.25rem;
            }
            
            .section-title h2::after {
                bottom: -0.75rem;
                width: 3rem;
            }
            
            .whatsapp-float {
                width: 3.125rem;
                height: 3.125rem;
                font-size: 1.5rem;
                bottom: 1.25rem;
                right: 1.25rem;
            }
            
            .back-to-top {
                width: 2.8125rem;
                height: 2.8125rem;
                font-size: 1rem;
                bottom: 1.25rem;
                right: 5.3125rem;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
            
            .footer {
                padding: 5rem 0 1.5rem;
            }
            
            .footer::before {
                height: 6.25rem;
            }
        }
        .amenities, .gallery, .location
        {
            padding:10px !important;
        }
        .contact-form 
        {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            max-width: 500px;
            margin: 0 auto;
        }

        .contact-form .form-control 
        {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--white);
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .contact-form .form-control::placeholder 
        {
            color: rgba(255, 255, 255, 0.6);
        }

        .contact-form .form-control:focus 
        {
            border-color: var(--secondary);
            box-shadow: none;
        }

        .contact-form textarea 
        {
            resize: none;
            height: 60px;
        }

        .contact-form .btn-primary 
        {
            width: 100%;
        }

        @media (max-width: 768px) 
        {
            .contact-form 
            {
                padding: 1.5rem;
            }
            .hero-content .row 
            {
                flex-direction: column-reverse;
            }
        }
        .navbar.solid 
        {
            background-color: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .navbar.transparent 
        {
            background-color: transparent;
            color: var(--primary);
            box-shadow: none;
        }

        .navbar.glass 
        {
            background-color: rgba(255, 255, 255, 0.2); 
            color: var(--text);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        @media (max-width: 767px) 
        {
            .project-name 
            {
                display: none;
            }
        }
    </style>
</head>
<body data-bs-spy="scroll" data-bs-target="#navbar">
    <nav class="navbar navbar-expand-lg fixed-top {{ $styleSettings['nav_style'] ?? 'solid' }}" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset($project->logo_path ? Storage::url($project->logo_path) : 'defaults/project-logo.png') }}" alt="Logo">
                <span class="project-name">{{ $project->name ?? 'Residences' }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#overview">Overview</a>
                    </li>
                    @if(!empty($project->amenities))
                    <li class="nav-item">
                        <a class="nav-link" href="#amenities">Amenities</a>
                    </li>
                    @endif
                    @if(!empty($project->gallery_images))
                    <li class="nav-item">
                        <a class="nav-link" href="#gallery">Gallery</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="#location">Location</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="hero-banner" id="heroBanner">
            <img src="{{ asset($project->cover_image_path ? Storage::url($project->cover_image_path) : 'defaults/project-cover.jpg') }}" class="active" alt="Project Banner">
        </div>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-center text-lg-start">
                        <p class="fade-in">{{ $project->short_description ?? 'A sanctuary of luxury, where elegance meets unmatched sophistication.' }}</p>
                        @if(!empty($project->price))
                            <div class="price-badge fade-in-up" style="animation-delay: 0.3s; font-size:16px;">
                                @if(!empty($project->price_display))
                                    {{ $project->price_display }}<br>
                                @endif
                                ₹{{ number_format($project->price) }} {{ $project->price_unit ?? '' }}
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <form class="contact-form fade-in-up mx-auto" style="animation-delay: 0.5s; max-width: 400px;" action="{{ route('submit.inquiry') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12 d-flex gap-3">                     
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" id="name" name="name" placeholder="Your Name" required>
                                    </div>
                                    <div class="col-6">         
                                        <input type="tel" class="form-control form-control-sm" id="phone" name="phone" placeholder="Your Phone Number" required>
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-3">
                                    @if(in_array('email', $formFields))
                                    <div class="col-6">                        
                                        <input type="text" class="form-control form-control-sm" id="email" name="email" placeholder="Your Email" required>
                                    </div>
                                    @endif
                                    @if(in_array('address', $formFields))
                                    <div class="col-6">
                                        <input type="tel" class="form-control form-control-sm" id="address" name="address" placeholder="Your Address" required>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-12 d-flex gap-3 mb-3">
                                    @if(in_array('state', $formFields))
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="field1" id="state" required>
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $state)
                                                <option value="{{ $state->state }}">{{ $state->state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    @if(in_array('city', $formFields))
                                    <div class="col-6">
                                        <select class="form-select form-select-sm" name="field2" id="city" required>
                                            <option value="">-- Select City --</option>
                                        </select>
                                    </div>
                                    @endif
                                </div>
                                @if(in_array('budget', $formFields))
                                <div class="col-12">
                                    <select class="form-select form-select-sm" name="budget" id="budget">
                                        <option value="">Select Budget</option>
                                        <option value="10L-20L" {{ (isset($lead) && $lead->budget == '10L-20L') || old('budget') == '10L-20L' ? 'selected' : '' }}>₹10 Lakh - ₹20 Lakh</option>
                                        <option value="20L-30L" {{ (isset($lead) && $lead->budget == '20L-30L') || old('budget') == '20L-30L' ? 'selected' : '' }}>₹20 Lakh - ₹30 Lakh</option>
                                        <option value="30L-40L" {{ (isset($lead) && $lead->budget == '30L-40L') || old('budget') == '30L-40L' ? 'selected' : '' }}>₹30 Lakh - ₹40 Lakh</option>
                                        <option value="40L-50L" {{ (isset($lead) && $lead->budget == '40L-50L') || old('budget') == '40L-50L' ? 'selected' : '' }}>₹40 Lakh - ₹50 Lakh</option>
                                        <option value="50L-60L" {{ (isset($lead) && $lead->budget == '50L-60L') || old('budget') == '50L-60L' ? 'selected' : '' }}>₹50 Lakh - ₹60 Lakh</option>
                                        <option value="60L-70L" {{ (isset($lead) && $lead->budget == '60L-70L') || old('budget') == '60L-70L' ? 'selected' : '' }}>₹60 Lakh - ₹70 Lakh</option>
                                        <option value="70L-80L" {{ (isset($lead) && $lead->budget == '70L-80L') || old('budget') == '70L-80L' ? 'selected' : '' }}>₹70 Lakh - ₹80 Lakh</option>
                                        <option value="80L-90L" {{ (isset($lead) && $lead->budget == '80L-90L') || old('budget') == '80L-90L' ? 'selected' : '' }}>₹80 Lakh - ₹90 Lakh</option>
                                        <option value="90L-1Cr" {{ (isset($lead) && $lead->budget == '90L-1Cr') || old('budget') == '90L-1Cr' ? 'selected' : '' }}>₹90 Lakh - ₹1 Crore</option>
                                        <option value="1Cr-1.25Cr" {{ (isset($lead) && $lead->budget == '1Cr-1.25Cr') || old('budget') == '1Cr-1.25Cr' ? 'selected' : '' }}>₹1 Crore - ₹1.25 Crore</option>
                                        <option value="1.25Cr-1.5Cr" {{ (isset($lead) && $lead->budget == '1.25Cr-1.5Cr') || old('budget') == '1.25Cr-1.5Cr' ? 'selected' : '' }}>₹1.25 Crore - ₹1.5 Crore</option>
                                        <option value="1.5Cr-1.75Cr" {{ (isset($lead) && $lead->budget == '1.5Cr-1.75Cr') || old('budget') == '1.5Cr-1.75Cr' ? 'selected' : '' }}>₹1.5 Crore - ₹1.75 Crore</option>
                                        <option value="1.75Cr-2Cr" {{ (isset($lead) && $lead->budget == '1.75Cr-2Cr') || old('budget') == '1.75Cr-2Cr' ? 'selected' : '' }}>₹1.75 Crore - ₹2 Crore</option>
                                        <option value="2Cr-2.25Cr" {{ (isset($lead) && $lead->budget == '2Cr-2.25Cr') || old('budget') == '2Cr-2.25Cr' ? 'selected' : '' }}>₹2 Crore - ₹2.25 Crore</option>
                                        <option value="2.25Cr-3Cr" {{ (isset($lead) && $lead->budget == '2.25Cr-3Cr') || old('budget') == '2.25Cr-3Cr' ? 'selected' : '' }}>₹2.25 Crore - ₹3 Crore</option>
                                        <option value="3Cr-3.5Cr" {{ (isset($lead) && $lead->budget == '3Cr-3.5Cr') || old('budget') == '3Cr-3.5Cr' ? 'selected' : '' }}>₹3 Crore - ₹3.5 Crore</option>
                                        <option value="3.5Cr-5Cr" {{ (isset($lead) && $lead->budget == '3.5Cr-5Cr') || old('budget') == '3.5Cr-5Cr' ? 'selected' : '' }}>₹3.5 Crore - ₹5 Crore</option>
                                        <option value="5Cr-10Cr" {{ (isset($lead) && $lead->budget == '5Cr-10Cr') || old('budget') == '5Cr-10Cr' ? 'selected' : '' }}>₹5 Crore - ₹10 Crore</option>
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="inquiry_question" class="form-label small fw-semibold">Select Inquiry Question</label>
                                <select id="inquiry_question" name="inquiry_question" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>Select a question</option>
                                    @foreach($inquiryQuestions as $question)
                                        <option value="{{ $question->id }}">{{ $question->question_text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label small fw-semibold">Your Message</label>
                                <textarea class="form-control form-control-sm" id="description" name="description" placeholder="Your Message" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-cust btn-sm rounded-pill w-100 fw-bold">
                                Submit Inquiry
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section overview" id="overview">
    <div class="container">
        <div class="section-title">
            <h2>{{ $project->name ?? 'Our Masterpiece' }}</h2>
            <div class="project-type-heading fade-in-up" style="animation-delay: 0.2s;">
                <h3 class="sub-heading">
                    {{ $project->type ?? 'Residential' }} | {{ $project->category_name ?? 'Apartments' }} | {{ $project->sub_category_name ?? 'Studio Apartment' }}
                </h3>
            </div>
            <p>{{ $project->short_description ?? 'A timeless blend of luxury and innovation, crafted for the elite.' }}</p>
        </div>
        <div class="row g-5">
            <div class="col-lg-6">
                @if(!empty($project->specifications))
                    @php $specs = json_decode($project->specifications, true) ?? []; @endphp
                    @if(!empty($specs))
                        <div class="row g-4 mb-5">
                            @if(isset($specs['beds']))
                            <div class="col-md-6">
                                <div class="card highlight-card fade-in-up" style="animation-delay: 0.3s">
                                    <div class="card-body">
                                        <h3>{{ $specs['beds'] }}</h3>
                                        <p>Bedrooms</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(isset($specs['baths']))
                            <div class="col-md-6">
                                <div class="card highlight-card fade-in-up" style="animation-delay: 0.4s">
                                    <div class="card-body">
                                        <h3>{{ $specs['baths'] }}</h3>
                                        <p>Bathrooms</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(isset($specs['size']))
                            <div class="col-md-6">
                                <div class="card highlight-card fade-in-up" style="animation-delay: 0.5s">
                                    <div class="card-body">
                                        <h3>{{ $specs['size'] }}</h3>
                                        <p>Area</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(isset($specs['balcony']))
                            <div class="col-md-6">
                                <div class="card highlight-card fade-in-up" style="animation-delay: 0.6s">
                                    <div class="card-body">
                                        <h3>{{ $specs['balcony'] }}</h3>
                                        <p>Balconies</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-lg-6">
                <div class="video-container fade-in-up" style="animation-delay: 0.6s">
                    @if(!empty($project->video_link))
                        @php
                            $isYouTube = preg_match('/(youtube\.com|youtu\.be)/i', $project->video_link);
                            $youtubeId = '';
                            if ($isYouTube) {
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $project->video_link, $match)) {
                                    $youtubeId = $match[1];
                                }
                            }
                        @endphp
                        @if($isYouTube && $youtubeId)
                            <iframe
                                class="video-thumbnail"
                                width="100%"
                                height="100%"
                                src="https://www.youtube.com/embed/{{ $youtubeId }}?rel=0"
                                frameborder="0"
                                allowfullscreen
                                style="border-radius: var(--border-radius);"
                            ></iframe>
                        @else
                            <video class="video-thumbnail" autoplay muted loop playsinline>
                                <source src="{{ $project->video_link }}" type="video/mp4">
                            </video>
                            <div class="play-button" onclick="playVideo(this)">
                                <i class="fas fa-play"></i>
                            </div>
                        @endif
                    @else
                        <img class="video-thumbnail" src="{{ asset($project->cover_image_path ? Storage::url($project->cover_image_path) : 'defaults/project-cover.jpg') }}" alt="Project Image">
                    @endif
                </div>
            </div>
            
            <div class="project-description fade-in-up" style="animation-delay: 0.7s">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h3>About the Project</h3>
                        <p class="lead">
                            {{ $project->description ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}
                        </p>
                        @if(!empty($project->additional_description))
                            <p>
                                {{ $project->additional_description }}
                            </p>
                        @endif
                    </div>
                    @if(!empty($specs))
                        <div class="col-lg-6">
                            <h3>Specifications</h3>
                            <div class="row g-4 fade-in-up" style="animation-delay: 0.8s">
                                @foreach($specs as $key => $value)
                                    <div class="col-sm-6">
                                        <div class="card spec-item h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="spec-icon">
                                                        <i class="fas fa-{{ $key == 'beds' ? 'bed' : ($key == 'baths' ? 'bath' : ($key == 'balcony' ? 'umbrella-beach' : ($key == 'terrace' ? 'layer-group' : 'ruler-combined'))) }}"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-1 text-uppercase text-muted small">{{ ucfirst(str_replace('_', ' ', $key)) }}</h5>
                                                        <p class="mb-0 h5">{{ $value }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

    @if(!empty($project->amenities))
        <section class="section amenities" id="amenities">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Features & Amenities</h2>
                    <p class="text-muted">Indulge in world-class amenities designed for comfort.</p>
                </div>
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    @foreach(json_decode($project->amenities) as $index => $amenity)
                    <div class="col">
                        <div class="card shadow-sm border-0 text-center p-3 fade-in-up" style="animation-delay: {{ 0.3 + ($index * 0.1) }}s;">
                            <div class="amenity-icon mb-2 fs-3">
                                <i class="fas fa-{{ $amenity->icon ?? 'star' }}"></i>
                            </div>
                            <h6 class="card-title mb-0">{{ $amenity->name ?? $amenity }}</h6>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    @if(!empty($project->gallery_images))
    <section class="section gallery mt-5" id="gallery">
        <div class="container">
            <div class="section-title">
                <h2>Gallery</h2>
                <p>Explore the elegance of our residences through visuals.</p>
            </div>
            <div class="row g-3">
                @foreach(json_decode($project->gallery_images) as $index => $image)
                <div class="col-md-3 col-6">
                    <div class="gallery-item fade-in-up" style="animation-delay: {{ 0.3 + ($index * 0.1) }}s" data-bs-toggle="modal" data-bs-target="#galleryModal" data-image="{{ Storage::url($image) }}">
                        <img src="{{ Storage::url($image) }}" class="img-fluid" alt="Gallery Image {{ $index + 1 }}">
                        <div class="gallery-overlay">
                            <i class="fas fa-expand"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section download" id="download">
        <div class="container">
            <div class="section-title">
                <h2>Download Resources</h2>
                <p>Access detailed information about our project.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-3">
                    <a href="{{ asset('storage/project_files/' . basename($project->brochure_path)) }}" 
                       class="btn price-badge w-100 fade-in-up" 
                       style="animation-delay: 0.2s" 
                       target="_blank" 
                       download>
                        Download Brochure
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ asset('storage/project_files/' . basename($project->floor_plan_path)) }}"
                    class="btn price-badge w-100 fade-in-up"
                    style="animation-delay: 0.3s"
                    download>
                        Download Floor Plan
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ asset('storage/project_files/' . basename($project->site_map_path)) }}"
                    class="btn price-badge w-100 fade-in-up"
                    style="animation-delay: 0.4s"
                    download>
                        Download Site Map
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ asset('storage/project_files/' . basename($project->price_list_path)) }}"
                    class="btn price-badge w-100 fade-in-up"
                    style="animation-delay: 0.5s"
                    download>
                        Download Price List
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    @include('modals.gallerymodal');    
    @endif

    <section class="section location" id="location">
        <div class="container">
            <div class="section-title">
                <h2>Prime Location</h2>
                <p>Situated in a prestigious address with excellent connectivity.</p>
            </div>
            <div class="row g-5 align-items-start">
                <div class="col-lg-6">
                    @php
                        // Default coordinates set to New Delhi; update these if needed
                        $lat = $project->latitude ?? '28.5928893';
                        $lng = $project->longitude ?? '77.2094153';
                    @endphp
                    <div class="location-map fade-in-up" style="animation-delay: 0.4s">
                        <div id="map" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex flex-column">
                    <div class="location-content fade-in-up" style="animation-delay: 0.6s">
                        <h3>Location Details</h3>
                        <div class="card location-address mb-4">
                            <div class="d-flex align-items-center gap-3 p-3">
                                <div class="location-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <p class="mb-0">{{ $project->location ?? 'Prime Locale' }}, {{ $project->city ?? 'New Delhi' }}, {{ $project->state ?? 'Delhi' }}</p>
                            </div>
                        </div>
                        @if(!empty($project->nearby_locations))
                            <div class="nearby-locations mt-5">
                                <h4 class="fw-semibold mb-4">Nearby Landmarks</h4>
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    @foreach(json_decode($project->nearby_locations) as $index => $landmark)
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm fade-in-up" style="animation-delay: {{ 0.5 + ($index * 0.1) }}s;">
                                                <div class="card-body d-flex align-items-start gap-3">
                                                    <div class="landmark-icon fs-3">
                                                        <i class="fas fa-map-pin"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold text-dark">
                                                            {{ $landmark->building ?? 'Landmark' }}
                                                        </h6>
                                                        <p class="mb-0 text-muted small">
                                                            {{ $landmark->location ?? 'Nearby' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a href="#" class="footer-logo">
                        <img src="{{ asset($project->logo_path ? Storage::url($project->logo_path) : 'defaults/project-logo.png') }}" alt="Logo">
                    </a>
                    <p>{{ $project->footer_description ?? 'A sanctuary of luxury, crafted for the discerning elite.' }}</p>
                    <div class="social-links">
                        @if(!empty($project->instagram_link))
                            <a href="{{ $project->instagram_link }}" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(!empty($project->facebook_link))
                            <a href="{{ $project->facebook_link }}" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(!empty($project->twitter_link))
                            <a href="{{ $project->twitter_link }}" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if(!empty($project->linkedin_link))
                            <a href="{{ $project->linkedin_link }}" target="_blank">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Explore</h4>
                    <ul>
                        <li>
                            <a href="#overview">Overview</a>
                        </li>
                        @if(!empty($project->gallery_images))
                            <li>
                                <a href="#gallery">Gallery</a>
                            </li>
                        @endif
                        @if(!empty($project->amenities))
                            <li>
                                <a href="#amenities">Amenities</a>
                            </li>
                        @endif
                        <li>
                            <a href="#location">Location</a>
                        </li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <div class="contact-info">
                        @if(!empty($project->contact_number_1))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Phone</h5>
                                    <a href="tel:{{ $project->contact_number_1 }}">{{ $project->contact_number_1 }}</a>
                                </div>
                            </div>
                        @endif
                        @if(!empty($project->whatsapp_number))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>WhatsApp</h5>
                                    <a href="https://wa.me/{{ $project->whatsapp_number }}" target="_blank">{{ $project->whatsapp_number }}</a>
                                </div>
                            </div>
                        @endif
                        @if(!empty($project->email))
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Email</h5>
                                    <a href="mailto:{{ $project->email }}">{{ $project->email }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $project->name ?? 'Ultra Residences' }}. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    @if(!empty($project->whatsapp_number))
        <a href="https://wa.me/{{ $project->whatsapp_number }}" class="whatsapp-float" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.addEventListener('scroll', function() 
        {
            const navbar = document.querySelector('.navbar');
            const backToTop = document.getElementById('backToTop');
            
            if (window.scrollY > 60) 
            {
                navbar.classList.add('scrolled');
            } 
            else 
            {
                navbar.classList.remove('scrolled');
            }
            
            if (window.scrollY > 300) 
            {
                backToTop.classList.add('active');
            } 
            else 
            {
                backToTop.classList.remove('active');
            }
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) 
                {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                    bsCollapse.hide();
                }
            });
        });
        const heroBanner = document.getElementById('heroBanner');
        if (heroBanner) 
        {
            const bannerImages = heroBanner.querySelectorAll('img');
            let currentBannerIndex = 0;
            if (bannerImages.length > 1) 
            {
                setInterval(() => {
                    const nextBannerIndex = (currentBannerIndex + 1) % bannerImages.length;
                    bannerImages[currentBannerIndex].classList.remove('active');
                    bannerImages[currentBannerIndex].classList.add('next');
                    bannerImages[nextBannerIndex].classList.remove('next');
                    bannerImages[nextBannerIndex].classList.add('active');
                    currentBannerIndex = nextBannerIndex;
                }, 5000); 
            }
        }
        @if(!empty($project->gallery_images))
            let currentImageIndex = 0;
            const galleryImages = [
                @foreach(json_decode($project->gallery_images) as $image)
                    '{{ Storage::url($image) }}',
                @endforeach
            ];
            document.querySelectorAll('.gallery-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    currentImageIndex = index;
                    document.getElementById('modalImage').src = galleryImages[currentImageIndex];
                });
            });

            function prevImage() 
            {
                currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : galleryImages.length - 1;
                document.getElementById('modalImage').src = galleryImages[currentImageIndex];
            }

            function nextImage() 
            {
                currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
                document.getElementById('modalImage').src = galleryImages[currentImageIndex];
            }

            document.addEventListener('keydown', (e) => {
                const modal = document.getElementById('galleryModal');
                if (modal.classList.contains('show')) 
                {
                    if (e.key === 'Escape') 
                    {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                    } 
                    else if (e.key === 'ArrowLeft') prevImage();
                    else if (e.key === 'ArrowRight') nextImage();
                }
            });
        @endif

        function playVideo(button) 
        {
            const videoContainer = button.parentElement;
            const video = videoContainer.querySelector('video');
            
            if (video.paused) 
            {
                video.play();
                video.controls = true;
                button.style.opacity = '0';
                button.style.pointerEvents = 'none';
            } 
            else
            {
                video.pause();
                video.controls = false;
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto';
            }
        }
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) 
            {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) 
                {
                    const navbarHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        document.getElementById('backToTop').addEventListener('click', function(e) 
        {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.fade-in-up');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) 
                    {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            elements.forEach(element => {
                observer.observe(element);
            });
        };
        document.addEventListener('DOMContentLoaded', () => {
            animateOnScroll();
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) 
            {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        window.addEventListener('resize', () => {
            animateOnScroll();
        }); 
        $(document).ready(function () 
        {
            $('#state').on('change', function () 
            {
                const state = $(this).val();
                if (state) 
                {
                    $.ajax({
                        url: '/lead/get-cities/' + encodeURIComponent(state),
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) 
                        {
                            const $city = $('#city');
                            $city.empty().append('<option value="">-- Select City --</option>');

                            if (Array.isArray(data)) 
                            {
                                data.forEach(function (item) 
                                {
                                    $city.append(`<option value="${item.District}">${item.District}</option>`);
                                });
                            }
                        },
                        error: function () 
                        {
                            alert('Something went wrong while fetching cities.');
                        }
                    });
                } 
                else 
                {
                    $('#city').empty().append('<option value="">-- Select City --</option>');
                }
            });
        });
    </script>
    <script>
        function initMap() 
        {
            const mapElement = document.getElementById('map');
            if (!mapElement) 
            {
                console.error('Map element not found. Please ensure <div id="map"> exists in the DOM.');
                return;
            }
            let lat = parseFloat('{{ $project->latitude ?? "28.5928893" }}');
            let lng = parseFloat('{{ $project->longitude ?? "77.2094153" }}');
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.warn('Invalid or missing coordinates. Using fallback: New Delhi (28.5928893, 77.2094153)');
                lat = 28.5928893;
                lng = 77.2094153;
            }

            console.log('Map initialized with coordinates:', { lat, lng });
            const map = L.map(mapElement).setView([lat, lng], 4);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
                tileSize: 512,
                zoomOffset: -1
            }).addTo(map);
            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`
                <div style="font-family: var(--font-heading); color: var(--text);">
                    <h5>{{ $project->name ?? "Ultra Residences" }}</h5>
                    <p style="margin: 0; font-size: 0.9rem;">
                        {{ $project->location ?? "Prime Locale" }}, 
                        {{ $project->city ?? "New Delhi" }}, 
                        {{ $project->state ?? "Delhi" }}
                    </p>
                    <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" style="display: block; margin-top: 8px; color: var(--secondary); text-decoration: none;">
                        View in Google Maps
                    </a>
                </div>
            `).openPopup();
            marker.on('click', () => {
                window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
            });

            const customIcon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                shadowSize: [41, 41]
            });
            marker.setIcon(customIcon);
        }

        document.addEventListener('DOMContentLoaded', () => {
            try 
            {
                initMap();
            } 
            catch (error) 
            {
                console.error('Error initializing map:', error);
            }
        });
    </script>
</body>
</html>