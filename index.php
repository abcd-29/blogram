<?php include 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogram - Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Base Layout Adjustments */
        :root {
            --navbar-height-lg: 70px;
            --navbar-height-md: 65px;
            --navbar-height-sm: 60px;
            --navbar-height-xs: 55px;
        }

        /* Component Spacing and Sizing */
        @media (min-width: 1200px) {
            .hero-content {
                max-width: 800px;
                padding: 3rem;
            }

            .featured-blogs {
                padding: 4rem;
                gap: 2.5rem;
            }

            .why-blogram {
                padding: 5rem 3rem;
                margin: 4rem auto;
            }
        }

        /* Laptop and Small Desktop */
        @media (max-width: 1199px) {
            .hero-content {
                max-width: 700px;
                padding: 2.5rem;
            }

            .featured-blogs {
                padding: 3rem;
                gap: 2rem;
            }

            .why-blogram {
                padding: 4rem 2.5rem;
                margin: 3.5rem auto;
            }
        }

        /* Tablets */
        @media (max-width: 992px) {
            .hero-content {
                max-width: 600px;
                padding: 2rem;
            }

            .featured-blogs {
                padding: 2.5rem;
                gap: 1.8rem;
            }

            .why-blogram {
                padding: 3.5rem 2rem;
                margin: 3rem 1.5rem;
            }

            .blog-card {
                width: calc(50% - 1rem);
            }
        }

        /* Large Phones */
        @media (max-width: 768px) {
            .navbar {
                height: var(--navbar-height-lg);
                padding: 0 1.2rem;
            }

            .hero-content {
                max-width: 90%;
                padding: 1.8rem;
                margin-top: var(--navbar-height-lg);
            }

            .featured-blogs {
                padding: 2rem 1.5rem;
                flex-direction: column;
                align-items: center;
            }

            .blog-card {
                width: 100%;
                max-width: 500px;
                margin: 1rem 0;
            }

            .why-blogram {
                padding: 3rem 1.5rem;
                margin: 2.5rem 1rem;
            }
        }

        /* Medium Phones */
        @media (max-width: 576px) {
            .navbar {
                height: var(--navbar-height-md);
                padding: 0 1rem;
            }

            .hero-content {
                padding: 1.5rem;
                margin-top: var(--navbar-height-md);
            }

            .hero h1 {
                font-size: 2rem;
            }

            .featured-blogs {
                padding: 1.8rem 1.2rem;
            }

            .blog-card {
                margin: 0.8rem 0;
            }

            .why-blogram {
                padding: 2.5rem 1.2rem;
                margin: 2rem 0.8rem;
            }
        }

        /* Small Phones */
        @media (max-width: 375px) {
            .navbar {
                height: var(--navbar-height-sm);
                padding: 0 0.8rem;
            }

            .hero-content {
                padding: 1.2rem;
                margin-top: var(--navbar-height-sm);
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .featured-blogs {
                padding: 1.5rem 1rem;
            }

            .blog-card {
                margin: 0.6rem 0;
            }

            .why-blogram {
                padding: 2rem 1rem;
                margin: 1.5rem 0.6rem;
            }
        }

        /* Extra Small Phones */
        @media (max-width: 320px) {
            .navbar {
                height: var(--navbar-height-xs);
                padding: 0 0.6rem;
            }

            .hero-content {
                padding: 1rem;
                margin-top: var(--navbar-height-xs);
            }

            .hero h1 {
                font-size: 1.6rem;
            }

            .featured-blogs {
                padding: 1.2rem 0.8rem;
            }

            .blog-card {
                margin: 0.5rem 0;
            }

            .why-blogram {
                padding: 1.8rem 0.8rem;
                margin: 1.2rem 0.5rem;
            }
        }

        /* Safe Area Insets for Modern Devices */
        @supports (padding: max(0px)) {
            .navbar,
            .hero-content,
            .featured-blogs,
            .why-blogram,
            .footer {
                padding-left: max(env(safe-area-inset-left), var(--component-padding, 1rem));
                padding-right: max(env(safe-area-inset-right), var(--component-padding, 1rem));
            }
        }

        /* Landscape Mode Adjustments */
        @media (max-height: 480px) and (orientation: landscape) {
            .hero-content {
                padding: 1rem;
                margin-top: var(--navbar-height-xs);
            }

            .featured-blogs,
            .why-blogram {
                padding: 1.5rem;
            }
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: #f4f4f4;
            color: #333;
            overflow-x: hidden;
        }
        nav {
            background-color: rgba(51, 51, 51, 0.8); /* Transparent background */
            padding: 10px 30px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed; /* Fixed position */
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background 0.3s ease-in-out;
        }
        nav .logo {
            display: flex;
            align-items: center;
        }
        nav .logo img {
            width: auto;
            height: 70px;
            margin-right: 10px;
        }
        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            margin-left: 5px;
            margin-right: 30px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }
        nav ul li a:hover {
            background: #ff2d2d;
            color: white;
        }
        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            padding: 10px;
            height: 40px;
            margin-left: auto;
        }
        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        @media (max-width: 768px) {
            nav {
                padding: 0 15px;
                height: 60px;
            }
            nav ul {
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: -100%;
                width: 100%;
                background: rgba(51, 51, 51, 0.8); /* Transparent background */
                transition: left 0.3s ease-in-out;
            }
            nav ul.show {
                left: 0;
            }
            nav ul li {
                margin: 10px 0;
            }
            .menu-toggle {
                display: flex;
                align-items: center;
            }
            .nav-links {
                position: fixed;
                top: 60px; /* Match navbar height */
                right: -100%;
                width: 250px;
                height: calc(100vh - 60px);
                background: rgba(31, 41, 55, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 20px;
                transition: right 0.3s ease;
            }
            .nav-links.active {
                right: 0;
            }
            .nav-buttons {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .nav-btn {
                width: 100%;
                text-align: center;
            }
        }
        @media (max-width: 480px) {
            nav {
                padding: 0 10px;
                height: 55px;
            }
            nav ul li a {
                padding: 8px 10px;
            }
            .menu-toggle {
                height: 35px;
                padding: 8px;
            }
            .menu-toggle span {
                width: 22px;
                height: 2px;
            }
            .nav-links {
                top: 55px; /* Match navbar height */
                height: calc(100vh - 55px);
            }
        }
        @media (max-width: 360px) {
            .navbar {
                height: 50px;
            }
            .nav-logo img {
                height: 32px;
            }
            .menu-toggle {
                height: 32px;
                padding: 6px;
            }
            .menu-toggle span {
                width: 20px;
            }
            .nav-links {
                top: 50px; /* Match navbar height */
                height: calc(100vh - 50px);
            }
        }
        .hero {
            position: relative;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            padding: 3rem;
            margin: 0 auto;
            background: none;
            border-radius: 20px;
            border: none;

        }
        .hero h1 {
            font-family: 'Pacifico', cursive;
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            opacity: 0;
            animation: slideInFromTop 1s ease forwards 0.5s;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #ffffff;
            line-height: 1.6;
            opacity: 0;
        }
        .hero p:nth-of-type(1) {
            animation: slideInFromLeft 1s ease forwards 1s;
        }
        .hero p:nth-of-type(2) {
            animation: slideInFromRight 1s ease forwards 1.5s;
        }
        .hero .cta-button {
            opacity: 0;
            animation: fadeInUp 1s ease forwards 2s;
        }
        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            color: white;
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
        }
        .featured-blogs {
            display: flex;
            justify-content: space-between;
            margin: 0 20px;
            animation: fadeInUp 2s ease-in-out;
        }
        .blog-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 15px;
            padding: 20px;
            width: calc(33.333% - 30px); /* Three columns with margin */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 2s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .blog-card img {
            width: 100%;
            height: 200px; /* Fixed height for all images */
            object-fit: cover;
            border-radius: 10px;
        }
        .blog-card h3 {
            margin-top: 15px;
            font-size: 24px;
        }
        .blog-card p {
            flex-grow: 1;
            margin: 15px 0;
        }
        .blog-card .icons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .blog-card .icons i {
            color: #ff416c;
            margin-right: 10px;
        }
        .featured-blogs-heading {
            font-family: 'Pacifico', cursive;
            font-size: 3rem;
            color: black;
            text-align: center;
            margin: 60px 0 40px;
            text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.4);
            opacity: 0;
            transform: translateY(20px);

            animation: fadeInUp 0.8s ease forwards;
        }
        .why-blogram {
            position: relative;
            padding: 80px 20px;
            margin: 60px 20px;
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
        }
        .why-blogram::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('images/tasha-kostyuk-tHWaD7J9YIA-unsplash.jpg') no-repeat center center;
            background-size: cover;
            filter: blur(8px);
            opacity: 0.7;
            z-index: -1;
        }
        .why-blogram h2 {
            color: black;
            font-size: 3.5rem;
            margin-bottom: 50px;
            font-family: 'Pacifico', cursive;
            text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.4);
            position: relative;
            z-index: 2;

        }
        .features-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            flex-wrap: nowrap;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-8px);
        }
        .feature-item i {
            font-size: 3rem;
            background: linear-gradient(135deg,rgb(255, 0, 0) 0%,rgb(255, 0, 0) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 1.2rem;
            border-radius: 50%;
            box-shadow: none;
            border:none;

        }
        .feature-item span {
            color: black;


            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        footer {
            background: rgba(31, 41, 55, 0.95);
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .footer-content p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 0; /* Hide scrollbar */
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInFromTop {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes slideInFromLeft {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideInFromRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* Navigation Bar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        /* Shrink navbar on scroll */
        .navbar.scrolled {
            height: 70px;
            background: rgba(31, 41, 55, 0.98);
        }
        .nav-logo {
            display: flex;
            align-items: center;
            height: 100%;
        }
        .nav-logo img {
            height: 60px;
            width: auto;
            transition: all 0.3s ease;
        }
        .navbar.scrolled .nav-logo img {
            height: 45px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .nav-btn {
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .login-btn {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        .signup-btn {
            color: #fff;
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
        }
        .signup-btn:hover {
            background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }
        /* Responsive Design */
        @media (max-width: 1200px) {
            .nav-btn {
                padding: 8px 20px;
                font-size: 0.95rem;
            }
            .features-container {
                gap: 2rem;
            }
            
            .feature-item i {
                font-size: 2.2rem;
            }
            
            .feature-item span {
                font-size: 1.2rem;
            }
        }
        @media (max-width: 992px) {
            .hero-content {
                max-width: 90%;
                padding: 2rem;
            }
            
            .nav-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
            .features-container {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .feature-item {
                flex: 0 1 calc(33.333% - 2rem);
            }
            
            .feature-item span {
                font-size: 1.1rem;
            }
            .featured-blogs {
                flex-direction: column;
                align-items: center;
                gap: 2rem;
            }
            .blog-card {
                width: 90%;
                max-width: 500px;
                margin: 0 auto;
            }
        }
        @media (max-width: 768px) {
            .hero-content {
                width: 90%;
                max-width: 400px;
                padding: 1.5rem;
            }
            .hero h1 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            .hero p {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }
            .nav-buttons {
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
            }
            .nav-btn {
                width: 100%;
                text-align: center;
                padding: 12px 20px;
            }
            .featured-blogs-heading {
                font-size: 2.5rem;
            }
            .why-blogram h2 {
                font-size: 2.5rem;
                margin-bottom: 40px;
            }
            
            .feature-item {
                flex: 0 1 calc(50% - 2rem);
            }
            
            .feature-item i {
                font-size: 2rem;
                padding: 1rem;
            }
            
            .feature-item span {
                font-size: 1rem;
            }
            .blog-card {
                width: 95%;
            }
        }
        @media (max-width: 480px) {
            .hero-content {
                padding: 1.5rem;
                margin: 0 1rem;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .nav-btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
            .cta-button {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
            .why-blogram {
                margin: 30px 10px;
                padding: 50px 10px;
            }
            
            .features-container {
                flex-direction: column;
                gap: 1.5rem;
            }
            
            .feature-item {
                width: 100%;
            }
            
            .footer-content {
                font-size: 0.8rem;
            }
            .why-blogram .features-container {
                padding: 0 10px;
            }
            .blog-card {
                width: 100%;
            }
        }
        @media (max-width: 360px) {
            .navbar {
                padding: 0 0.5rem;
            }
            .nav-logo img {
                height: 35px;
            }
            .nav-btn {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
        }
        /* Media Queries for Different Mobile Sizes */

        /* Large Phones (iPhone Pro Max, Samsung S21 Ultra etc.) */
        @media (max-width: 428px) {
            .navbar {
                height: 65px;
                padding: 0 12px;
            }

            .nav-logo img {
                height: 42px;
            }

            .menu-toggle {
                height: 38px;
                padding: 8px;
            }

            .nav-links {
                top: 65px;
                height: calc(100vh - 65px);
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 0.95rem;
            }
        }

        /* Medium Phones (iPhone 12, Samsung S21 etc.) */
        @media (max-width: 390px) {
            .navbar {
                height: 60px;
                padding: 0 10px;
            }

            .nav-logo img {
                height: 35px;
            }

            .menu-toggle {
                height: 35px;
                padding: 7px;
            }

            .menu-toggle span {
                width: 22px;
                height: 2px;
            }

            .nav-links {
                top: 60px;
                height: calc(100vh - 60px);
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 0.9rem;
            }
        }

        /* Small Phones (iPhone Mini, SE etc.) */
        @media (max-width: 375px) {
            .navbar {
                height: 55px;
                padding: 0 8px;
            }

            .nav-logo img {
                height: 40px;
            }

            .menu-toggle {
                height: 32px;
                padding: 6px;
            }

            .menu-toggle span {
                width: 20px;
                height: 2px;
            }

            .nav-links {
                top: 55px;
                height: calc(100vh - 55px);
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .hero p {
                font-size: 0.85rem;
            }
        }

        /* Extra Small Phones */
        @media (max-width: 320px) {
            .navbar {
                height: 50px;
                padding: 0 6px;
            }

            .nav-logo img {
                height: 38px;
            }

            .menu-toggle {
                height: 28px;
                padding: 5px;
            }

            .menu-toggle span {
                width: 18px;
                height: 2px;
            }

            .nav-links {
                top: 50px;
                height: calc(100vh - 50px);
            }

            .hero h1 {
                font-size: 1.6rem;
            }

            .hero p {
                font-size: 0.8rem;
            }
        }

        /* Portrait vs Landscape */
        @media (max-height: 480px) and (orientation: landscape) {
            .navbar {
                height: 50px;
            }

            .nav-logo img {
                height: 40px;
            }

            .menu-toggle {
                height: 30px;
            }

            .nav-links {
                top: 50px;
                height: calc(100vh - 50px);
                overflow-y: auto;
            }
        }

        /* High-DPI Screens */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .nav-logo img {
                transform: translateZ(0);
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
        }

        /* Ensure proper spacing for devices with notches */
        @supports (padding: max(0px)) {
            .navbar {
                padding-left: max(10px, env(safe-area-inset-left));
                padding-right: max(10px, env(safe-area-inset-right));
            }
        }

        /* Navigation Links Order */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        /* Mobile Navigation */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .nav-link {
                width: 100%;
                padding: 12px 20px;
                border-radius: 8px;
            }

            .nav-links a:nth-child(1) { order: 1; } /* Home */
            .nav-links a:nth-child(2) { order: 2; } /* Blogs */
            .nav-links a:nth-child(3) { order: 3; } /* Register */
            .nav-links a:nth-child(4) { order: 4; } /* Login */
        }

        /* Welcome Section Button Base Styles */
        .cta-buttons-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 2rem;
        }

        .cta-button {
            padding: 14px 28px;
            font-size: 1rem;
            min-width: 140px;
        }

        /* Media Queries for Button Sizing */
        @media (max-width: 768px) {
            .cta-buttons-container {
                gap: 15px;
            }

            .cta-button {
                padding: 12px 24px;
                font-size: 0.95rem;
                min-width: 130px;
            }
        }

        @media (max-width: 576px) {
            .cta-buttons-container {
                gap: 12px;
            }

            .cta-button {
                padding: 10px 20px;
                font-size: 0.9rem;
                min-width: 120px;
            }
        }

        @media (max-width: 428px) {
            .cta-buttons-container {
                gap: 10px;
            }

            .cta-button {
                padding: 9px 18px;
                font-size: 0.85rem;
                min-width: 110px;
            }
        }

        @media (max-width: 375px) {
            .cta-buttons-container {
                gap: 8px;
            }

            .cta-button {
                padding: 8px 16px;
                font-size: 0.8rem;
                min-width: 100px;
            }
        }

        @media (max-width: 320px) {
            .cta-buttons-container {
                gap: 6px;
            }

            .cta-button {
                padding: 7px 14px;
                font-size: 0.75rem;
                min-width: 90px;
            }
        }

        /* Form Base Styles */
        .form-container {
            max-width: 500px;
            width: 90%;
            padding: 2.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            padding: 12px 15px;
            font-size: 1rem;
        }

        /* Form Size Media Queries */
        @media (max-width: 768px) {
            .form-container {
                max-width: 450px;
                padding: 2rem;
            }

            .form-group {
                margin-bottom: 1.2rem;
            }

            .form-control {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                max-width: 400px;
                padding: 1.8rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-control {
                padding: 9px 11px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 428px) {
            .form-container {
                max-width: 350px;
                padding: 1.5rem;
            }

            .form-group {
                margin-bottom: 0.9rem;
            }

            .form-control {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 375px) {
            .form-container {
                max-width: 320px;
                padding: 1.2rem;
            }

            .form-group {
                margin-bottom: 0.8rem;
            }

            .form-control {
                padding: 7px 9px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 320px) {
            .form-container {
                max-width: 290px;
                padding: 1rem;
            }

            .form-group {
                margin-bottom: 0.7rem;
            }

            .form-control {
                padding: 6px 8px;
                font-size: 0.75rem;
            }
        }

        /* Form Input Fields Size Adjustment */
        @media (max-width: 768px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            textarea,
            select {
                height: 40px;
            }
        }

        @media (max-width: 576px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            textarea,
            select {
                height: 38px;
            }
        }

        @media (max-width: 428px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            textarea,
            select {
                height: 36px;
            }
        }

        @media (max-width: 375px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            textarea,
            select {
                height: 34px;
            }
        }

        @media (max-width: 320px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            textarea,
            select {
                height: 32px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">
            <img src="images/Blogram-1-2-2025.png" alt="Blogram Logo">
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="blogs.php" class="nav-link">Blogs</a>
            <?php if (isLoggedIn()): ?>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="create_blog.php" class="nav-link">Create Blog</a>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="register.php" class="nav-link">Register</a>
                <a href="login.php" class="nav-link">Login</a>
            <?php endif; ?>
        </div>
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <div class="hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="images/school.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to Blogram</h1>
            <p>Share your thoughts and connect with others!</p>
            <div class="cta-buttons-container">
                <a href="register.php" class="cta-button">Get Started</a>
                <a href="blogs.php" class="cta-button">Explore Blogs</a>
            </div>
        </div>
    </div>

    <h2 class="featured-blogs-heading">Featured Blogs</h2>
    <div class="featured-blogs animate-on-scroll">
        <!-- Example Blog Cards -->
        <div class="blog-card">
            <img src="images/david-becker-ea4FS8zIsqE-unsplash.jpg" alt="Blog 1">
            <h3>Exploring the Mountains</h3>
            <p>Join us on an adventurous journey through the majestic mountains. Discover the beauty of nature and the thrill of hiking.</p>
            <div class="icons">
                <span><i class="fas fa-user"></i> John Doe</span>
                <span><i class="fas fa-calendar-alt"></i> Jan 1, 2023</span>
                <span><i class="fas fa-comments"></i> 5 Comments</span>
            </div>
        </div>
        <div class="blog-card">
            <img src="images/jonny-gios-49U_31wsJxU-unsplash.jpg" alt="Blog 2">
            <h3>City Life Adventures</h3>
            <p>Experience the hustle and bustle of city life. From skyscrapers to street food, explore what makes urban living exciting.</p>
            <div class="icons">
                <span><i class="fas fa-user"></i> Jane Smith</span>
                <span><i class="fas fa-calendar-alt"></i> Feb 15, 2023</span>
                <span><i class="fas fa-comments"></i> 10 Comments</span>
            </div>
        </div>
        <div class="blog-card">
            <img src="images/sean-lee-8p02cxiNGl4-unsplash.jpg" alt="Blog 3">
            <h3>Serenity by the Lake</h3>
            <p>Find peace and tranquility by the serene lake. A perfect getaway to relax and rejuvenate amidst nature's beauty.</p>
            <div class="icons">
                <span><i class="fas fa-user"></i> Alice Johnson</span>
                <span><i class="fas fa-calendar-alt"></i> Mar 10, 2023</span>
                <span><i class="fas fa-comments"></i> 8 Comments</span>
            </div>
        </div>
    </div>

    <div class="why-blogram animate-on-scroll">
        <h2>Why Blogram?</h2>
        <div class="features-container">
            <div class="feature-item">
                <i class="fas fa-pencil-alt"></i>
                <span>Enhance Writing Skills</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-user-tie"></i>
                <span>Build Personal Brand</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-comments"></i>
                <span>Engage in Discussions</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-book"></i>
                <span>Access Knowledge</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-mobile-alt"></i>
                <span>Stay Connected</span>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Blogram | Contact: contact@blogram.com</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const navUl = document.querySelector('nav ul');
            navUl.classList.toggle('show');
        }

        // Function to handle scroll animations
        function handleScrollAnimations() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            const windowHeight = window.innerHeight;

            elements.forEach(element => {
                const positionFromTop = element.getBoundingClientRect().top;

                if (positionFromTop - windowHeight <= 0) {
                    element.classList.add('visible');
                }
            });
        }

        window.addEventListener('scroll', handleScrollAnimations);
        window.addEventListener('load', handleScrollAnimations);

        // Add scroll event listener for navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');

            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
                
                // Animate hamburger to X
                const spans = this.querySelectorAll('span');
                if (navLinks.classList.contains('active')) {
                    spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-links') && !e.target.closest('.menu-toggle')) {
                    navLinks.classList.remove('active');
                    const spans = menuToggle.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });
        });

        // Close menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.querySelector('.nav-links').classList.remove('active');
                const spans = document.querySelectorAll('.menu-toggle span');
                spans.forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            }
        });

        // Ensure proper alignment of menu items on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            const logo = document.querySelector('.nav-logo');
            const menuToggle = document.querySelector('.menu-toggle');

            function adjustNavbarHeight() {
                if (window.innerWidth <= 768) {
                    const logoHeight = logo.offsetHeight;
                    const menuHeight = menuToggle.offsetHeight;
                    const maxHeight = Math.max(logoHeight, menuHeight);
                    navbar.style.height = `${maxHeight + 20}px`; // Adding padding
                } else {
                    navbar.style.height = '';
                }
            }

            window.addEventListener('resize', adjustNavbarHeight);
            adjustNavbarHeight();
        });

        // Add viewport height fix for mobile browsers
        function setVH() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }

        window.addEventListener('load', setVH);
        window.addEventListener('resize', setVH);
    </script>
</body>
</html>
