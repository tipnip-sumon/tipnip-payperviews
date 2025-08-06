<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{{ $pageTitle }} - {{ siteName() }} | Watch Videos & Earn Money</title>
    <meta name="description" content="{{ \App\Models\GeneralSetting::getSetting('meta_description', siteName() . ' - Watch amazing videos and earn money! Join our platform to start earning money by watching videos and build your wealth today.') }}">
    <meta name="keywords" content="{{ \App\Models\GeneralSetting::getSetting('meta_keywords', 'videos, watch videos, earn money, video gallery, entertainment, make money online') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="alternate icon" href="{{ \App\Models\GeneralSetting::getFavicon() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\GeneralSetting::getLogo() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --success-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.1);
            --border-radius: 20px;
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Navigation Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            padding: 1rem 0;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            padding: 0.5rem 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 800;
            text-decoration: none;
            transition: var(--transition);
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar-brand img {
            height: 45px;
            margin-right: 12px;
            transition: var(--transition);
        }

        .brand-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.8rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
        }

        .nav-link {
            font-weight: 500;
            color: #2c3e50 !important;
            transition: var(--transition);
            position: relative;
            margin: 0 0.5rem;
        }

        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-2px);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        /* Hero Section */
        .hero-section {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 120px 0 80px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #ffffff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.4rem);
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.7;
        }

        .hero-stats {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin: 2rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            font-weight: 500;
        }

        /* Button Styles */
        .btn-modern {
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-modern {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-primary-modern:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.3);
        }

        .btn-secondary-modern {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-secondary-modern:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.2);
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
            color: white;
        }

        /* Hero Image */
        .hero-image {
            position: relative;
            z-index: 2;
        }

        .hero-device {
            max-width: 100%;
            height: auto;
            border-radius: 30px;
            box-shadow: var(--shadow-heavy);
            transform: perspective(1000px) rotateY(-15deg) rotateX(10deg);
            transition: var(--transition);
        }

        .hero-device:hover {
            transform: perspective(1000px) rotateY(-10deg) rotateX(5deg) scale(1.02);
        }

        /* Business Showcase Styles */
        .business-showcase-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .main-feature-display {
            position: relative;
            min-height: 400px;
        }

        .feature-card-modern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transform: translateY(30px) scale(0.9);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card-modern.active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Dashboard Mockup */
        .dashboard-mockup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-mockup::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="2"/></g></svg>');
            animation: float 20s ease-in-out infinite;
        }

        .mockup-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .mockup-logo img {
            filter: brightness(0) invert(1);
        }

        .stat-pill {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .mockup-content {
            position: relative;
            z-index: 2;
        }

        .video-grid-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .mini-video-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mini-thumbnail {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .mini-info {
            text-align: left;
        }

        .mini-title {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .mini-earning {
            font-size: 0.75rem;
            color: #ffd700;
            font-weight: 700;
        }

        .earnings-display {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .earnings-counter {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 0.5rem;
        }

        .currency {
            color: #ffd700;
        }

        .amount {
            color: white;
        }

        .earnings-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Investment Showcase */
        .investment-showcase {
            padding: 2rem;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 20px;
            color: white;
            text-align: center;
        }

        .investment-header h5 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .investment-header p {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .access-plans {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        /* Access Plans Styling - Compact Version */
        .access-plans {
            padding: 0.5rem 0;
        }

        .plans-grid {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            align-items: stretch;
            flex-wrap: nowrap;
        }

        .plan-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            flex: 1;
            min-width: 120px;
            max-width: 140px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .plan-card.featured {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.03);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.1);
        }

        .plan-card.elite-plan {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.25), rgba(255, 140, 0, 0.15));
            border: 1.5px solid rgba(255, 215, 0, 0.5);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.2);
        }

        .plan-header {
            text-align: center;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .plan-icon {
            font-size: 1.5rem;
            margin: 0.25rem 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .popular-label, .vip-label {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            color: white;
            padding: 0.15rem 0.5rem;
            border-radius: 15px;
            font-size: 0.6rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
        }

        .vip-label {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #000;
        }

        .plan-pricing {
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .plan-amount {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
            font-family: 'Poppins', sans-serif;
            color: white;
        }

        .plan-period {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        .plan-features {
            flex-grow: 1;
            margin-bottom: 0.75rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .feature-item i {
            margin-right: 0.4rem;
            width: 12px;
            text-align: center;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .plan-footer {
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .withdraw-note {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.1);
            padding: 0.3rem;
            border-radius: 6px;
        }

        .withdraw-note i {
            margin-right: 0.3rem;
            font-size: 0.6rem;
        }

        .plan-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .plan-badge.basic {
            background: linear-gradient(45deg, #6c757d, #495057);
            color: white;
        }

        .plan-badge.premium {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #000;
        }

        .plan-badge.elite {
            background: linear-gradient(45deg, #ff6b35, #f7931e);
            color: white;
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
        }

        .plan-amount {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
            font-family: 'Poppins', sans-serif;
        }

        .plan-features {
            margin-bottom: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-align: left;
        }

        .feature-item i {
            width: 16px;
            flex-shrink: 0;
        }

        .withdraw-note {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .investment-notice {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .notice-content {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
        }

        .investment-chart {
            margin-bottom: 2rem;
        }

        .chart-bars {
            display: flex;
            justify-content: center;
            align-items: end;
            gap: 0.5rem;
            height: 100px;
            margin-bottom: 1rem;
        }

        .bar {
            width: 20px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            animation: growBar 1s ease-out forwards;
            transform: scaleY(0);
            transform-origin: bottom;
        }

        .bar:nth-child(odd) {
            background: rgba(255, 255, 255, 0.6);
        }

        @keyframes growBar {
            to {
                transform: scaleY(1);
            }
        }

        .chart-trend {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .investment-plans {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .plan-card h6 {
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .plan-return {
            font-size: 0.85rem;
            color: #ffd700;
            font-weight: 600;
        }

        /* Lottery Showcase */
        .lottery-showcase {
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            color: white;
            text-align: center;
        }

        .lottery-header h5 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .lottery-header p {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .lottery-content {
            margin-bottom: 1.5rem;
        }

        .lottery-ticket-display {
            margin-bottom: 2rem;
        }

        .ticket-sample {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            border: 2px dashed rgba(255, 255, 255, 0.3);
            margin: 0 auto;
            max-width: 200px;
            transform: perspective(1000px) rotateY(-5deg);
            transition: var(--transition);
        }

        .ticket-sample:hover {
            transform: perspective(1000px) rotateY(0deg) scale(1.05);
        }

        .ticket-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.3);
        }

        .ticket-logo {
            font-size: 1.2rem;
        }

        .ticket-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ticket-body {
            text-align: center;
        }

        .ticket-number {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
            font-family: 'Courier New', monospace;
        }

        .ticket-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffd700;
            margin-bottom: 0.75rem;
        }

        .draw-date {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .lottery-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .info-item i {
            font-size: 1.2rem;
        }

        .lottery-notice {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .lottery-wheel {
            width: 150px;
            height: 150px;
            margin: 0 auto 2rem;
            position: relative;
            border-radius: 50%;
            background: conic-gradient(#ff6b6b, #ffd93d, #6bcf7f, #4d96ff, #9c88ff, #ff6b6b);
            animation: spin 10s linear infinite;
        }

        .wheel-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-weight: 700;
            font-size: 0.8rem;
            z-index: 2;
        }

        .wheel-center i {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .lottery-prizes {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .prize-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        /* Referral Showcase */
        .referral-showcase {
            padding: 2rem;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border-radius: 20px;
            color: white;
            text-align: center;
        }

        .referral-network {
            position: relative;
            margin-bottom: 2rem;
            height: 120px;
        }

        .user-node {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-node.main-user {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.3);
            font-size: 0.8rem;
        }

        .referred-users .user-node:nth-child(1) {
            top: 10px;
            left: 20px;
        }

        .referred-users .user-node:nth-child(2) {
            top: 10px;
            right: 20px;
        }

        .referred-users .user-node:nth-child(3) {
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .referral-earnings {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .commission-counter {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 0.5rem;
        }

        .commission-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Feature Navigation */
        .feature-navigation {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .nav-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            color: #667eea;
            padding: 0.75rem 1rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
            backdrop-filter: blur(10px);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            min-width: 80px;
        }

        .nav-btn i {
            font-size: 1.2rem;
        }

        .nav-btn.active,
        .nav-btn:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        /* Video Gallery Section */
        .gallery-section {
            padding: 100px 0;
            position: relative;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.2rem;
            text-align: center;
            color: #6c757d;
            margin-bottom: 4rem;
        }

        /* Search and Filter */
        .search-filter-section {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            margin-bottom: 3rem;
        }

        .search-input {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .filter-btn {
            border: 2px solid #e9ecef;
            background: white;
            color: #6c757d;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 500;
            transition: var(--transition);
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-gradient);
            border-color: transparent;
            color: white;
        }

        /* Video Cards */
        .video-card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            height: 100%;
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }

        .video-thumbnail {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            aspect-ratio: 16/9;
        }

        .video-thumbnail iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .video-card:hover .video-overlay {
            opacity: 1;
        }

        .play-button {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            transition: var(--transition);
        }

        .play-button:hover {
            transform: scale(1.1);
            background: white;
            color: #667eea;
        }

        .stats-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .earning-badge {
            top: 60px;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #000;
        }

        .card-body-modern {
            padding: 2rem;
        }

        .video-title {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .video-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .video-stats {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-group {
            text-align: center;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }

        .stat-value.text-primary { color: #667eea !important; }
        .stat-value.text-success { color: #10b981 !important; }
        .stat-value.text-warning { color: #f59e0b !important; }

        .watch-btn {
            width: 100%;
            padding: 15px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .feature-card {
            background: white;
            padding: 3rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            text-align: center;
            transition: var(--transition);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 2rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        /* CTA Section */
        .cta-section {
            background: var(--primary-gradient);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }

        .cta-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(circle, white 2px, transparent 2px);
            background-size: 50px 50px;
            animation: float 30s ease-in-out infinite;
        }

        /* Footer */
        .footer-modern {
            background: var(--dark-gradient);
            color: white;
            padding: 80px 0 40px;
        }

        .footer-brand {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Poppins', sans-serif;
            margin-bottom: 1rem;
        }

        .social-links a {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            transition: var(--transition);
            color: white;
            text-decoration: none;
        }

        .social-links a:hover {
            background: white;
            color: #2c3e50;
            transform: translateY(-5px);
        }

        /* Newsletter */
        .newsletter-form {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 8px;
            display: flex;
            max-width: 400px;
        }

        .newsletter-input {
            background: transparent;
            border: none;
            color: white;
            padding: 12px 20px;
            flex: 1;
            border-radius: 50px;
        }

        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .newsletter-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }

        .newsletter-btn:hover {
            transform: scale(1.05);
            color: #667eea;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-device {
                transform: perspective(1000px) rotateY(-10deg) rotateX(5deg);
            }
        }

        @media (max-width: 992px) {
            .hero-section {
                padding: 100px 0 60px;
                text-align: center;
            }
            
            .hero-device {
                transform: none;
                margin-top: 3rem;
            }
            
            .hero-stats {
                margin: 3rem 0;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .search-filter-section {
                padding: 1.5rem;
            }
            
            .filter-btn {
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar-brand img {
                height: 35px;
            }
            
            .brand-text {
                font-size: 1.4rem;
            }
            
            .hero-section {
                padding: 80px 0 40px;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .btn-modern {
                padding: 12px 25px;
                font-size: 1rem;
                margin: 0.5rem;
            }
            
            .hero-stats {
                padding: 1.5rem;
            }
            
            .stat-item {
                padding: 0.5rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .gallery-section,
            .features-section,
            .cta-section {
                padding: 60px 0;
            }
            
            .search-filter-section {
                padding: 1rem;
            }
            
            .video-card {
                margin-bottom: 2rem;
            }
            
            .card-body-modern {
                padding: 1.5rem;
            }
            
            .feature-card {
                padding: 2rem 1.5rem;
                margin-bottom: 2rem;
            }
            
            .footer-modern {
                padding: 60px 0 30px;
            }
            
            .newsletter-form {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            /* Mobile responsive for investment showcase */
            .access-plans {
                padding: 0.25rem 0;
            }
            
            .plans-grid {
                flex-wrap: wrap;
                gap: 0.5rem;
                justify-content: center;
            }
            
            .plan-card {
                min-width: 100px;
                max-width: 130px;
                padding: 0.75rem;
            }
            
            .plan-card.featured {
                transform: none;
                order: -1;
            }
            
            .plan-card.elite-plan {
                transform: none;
                order: -2;
            }
            
            .popular-label, .vip-label {
                position: static;
                display: inline-block;
                margin-top: 0.25rem;
                font-size: 0.55rem;
                padding: 0.1rem 0.4rem;
            }
            
            .plan-amount {
                font-size: 1.3rem;
            }
            
            .plan-icon {
                font-size: 1.2rem;
            }
            
            .feature-item {
                font-size: 0.7rem;
                margin-bottom: 0.4rem;
            }
            
            .feature-item i {
                width: 10px;
                font-size: 0.65rem;
            }
            
            .withdraw-note {
                font-size: 0.65rem;
                padding: 0.25rem;
            }
            
            .investment-header h5 {
                font-size: 1.2rem;
            }
            
            .plan-amount {
                font-size: 1.5rem;
            }
            
            .feature-item {
                font-size: 0.8rem;
            }
            
            /* Mobile responsive for lottery showcase */
            .info-row {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .ticket-sample {
                max-width: 160px;
                transform: none;
            }
            
            .lottery-header h5 {
                font-size: 1.2rem;
            }
            
            .info-item {
                padding: 0.5rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .video-stats {
                padding: 1rem;
            }
            
            .stat-value {
                font-size: 1.2rem;
            }
        }

        /* Animation Classes */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: var(--transition);
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Lazy Loading Styles */
        .iframe-container {
            position: relative;
            width: 100%;
            height: 200px;
            border-radius: 15px;
            overflow: hidden;
        }

        .iframe-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .iframe-placeholder:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .placeholder-content {
            text-align: center;
            padding: 20px;
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 15px;
        }

        .iframe-container.loaded .iframe-placeholder {
            display: none;
        }

        /* Load More Button Animation */
        #loadMoreBtn {
            transition: all 0.3s ease;
            min-width: 200px;
        }

        #loadMoreBtn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Video Item Animation for newly loaded items */
        .video-item.newly-loaded {
            opacity: 0;
            transform: translateY(30px);
            animation: slideInUp 0.6s ease-out forwards;
        }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ \App\Models\GeneralSetting::getLogo() }}" alt="{{ siteName() }}">
                <span class="brand-text">{{ siteName() }}</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('videos.public') }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-primary btn-modern me-2" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-gradient btn-modern" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Join Now
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="hero-title">Watch Videos &<br>Earn Real Money!</h1>
                    <p class="hero-subtitle">
                        Welcome to {{ siteName() }} - The world's premier video earning platform where your entertainment time becomes income time. Join millions earning daily!
                    </p>
                    
                    <div class="hero-stats" data-aos="fade-up" data-aos-delay="200">
                        <div class="row">
                            <div class="col-4 stat-item">
                                <i class="fas fa-video stat-icon text-warning"></i>
                                <div class="stat-number">{{ number_format($totalVideos) }}+</div>
                                <div class="stat-label">Premium Videos</div>
                            </div>
                            <div class="col-4 stat-item">
                                <i class="fas fa-eye stat-icon text-info"></i>
                                <div class="stat-number">{{ number_format($totalViews) }}+</div>
                                <div class="stat-label">Total Views</div>
                            </div>
                            <div class="col-4 stat-item">
                                <i class="fas fa-dollar-sign stat-icon text-success"></i>
                                <div class="stat-number">${{ number_format($totalEarningsPaid, 0) }}+</div>
                                <div class="stat-label">Earnings Paid</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4" data-aos="fade-up" data-aos-delay="400">
                        <a href="{{ route('register') }}" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-rocket"></i> Start Earning Now
                        </a>
                        <a href="#gallery" class="btn btn-secondary-modern btn-modern">
                            <i class="fas fa-play"></i> Watch Preview
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6 hero-image" data-aos="fade-left" data-aos-delay="300">
                    <div class="text-center">
                        <!-- Dynamic Business Module Showcase -->
                        <div class="business-showcase-container position-relative">
                            <!-- Main Feature Image -->
                            <div class="main-feature-display">
                                <div class="feature-card-modern active" data-feature="video-earning">
                                    <div class="feature-visual">
                                        <div class="dashboard-mockup">
                                            <div class="mockup-header">
                                                <div class="mockup-logo">
                                                    <img src="{{ \App\Models\GeneralSetting::getLogo() }}" alt="{{ siteName() }}" style="height: 24px;">
                                                </div>
                                                <div class="mockup-stats">
                                                    <span class="stat-pill">${{ number_format($totalEarningsPaid, 0) }}+ Paid</span>
                                                </div>
                                            </div>
                                            <div class="mockup-content">
                                                <div class="video-grid-preview">
                                                    @foreach($videos->take(4) as $index => $video)
                                                        <div class="mini-video-card" style="animation-delay: {{ $index * 0.2 }}s">
                                                            <div class="mini-thumbnail">
                                                                <i class="fas fa-play"></i>
                                                            </div>
                                                            <div class="mini-info">
                                                                <div class="mini-title">{{ Str::limit($video->title, 20) }}</div>
                                                                <div class="mini-earning">${{ number_format($video->earning_per_view, 4) }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="earnings-display">
                                                    <div class="earnings-counter">
                                                        <span class="currency">$</span>
                                                        <span class="amount" id="earningsCounter">0.00</span>
                                                    </div>
                                                    <div class="earnings-label">Your Daily Earnings</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Investment Module -->
                                <div class="feature-card-modern" data-feature="investment">
                                    <div class="feature-visual">
                                        <div class="investment-showcase">
                                            <div class="investment-header">
                                                <h5 class="text-white mb-3">
                                                    <i class="fas fa-key me-2"></i>Video Access Plans
                                                </h5>
                                                <p class="text-white-50 mb-4">
                                                    Deposit required for video access • Withdraw anytime
                                                </p>
                                            </div>
                                            <div class="access-plans">
                                                <div class="plans-grid">
                                                    @php
                                                        // Always show at least 3 plans for better UI
                                                        $displayPlans = collect();
                                                        
                                                        // Add real plans if they exist
                                                        if(isset($plans) && count($plans) > 0) {
                                                            $displayPlans = $plans->take(3);
                                                        }
                                                        
                                                        // Fill remaining slots with fallback plans
                                                        $fallbackPlans = [
                                                            (object)[
                                                                'name' => 'Basic',
                                                                'fixed_amount' => 50,
                                                                'daily_video_limit' => 10,
                                                                'video_earning_rate' => 0.01,
                                                                'featured' => false
                                                            ],
                                                            (object)[
                                                                'name' => 'Standard',
                                                                'fixed_amount' => 100,
                                                                'daily_video_limit' => 25,
                                                                'video_earning_rate' => 0.02,
                                                                'featured' => true
                                                            ],
                                                            (object)[
                                                                'name' => 'Premium',
                                                                'fixed_amount' => 200,
                                                                'daily_video_limit' => 0,
                                                                'video_earning_rate' => 0.03,
                                                                'featured' => true
                                                            ]
                                                        ];
                                                        
                                                        // Add fallback plans to fill up to 3 total
                                                        for($i = $displayPlans->count(); $i < 3; $i++) {
                                                            $displayPlans->push((object)$fallbackPlans[$i]);
                                                        }
                                                    @endphp
                                                    
                                                    @foreach($displayPlans as $index => $plan)
                                                        <!-- {{ $plan->name }} Plan -->
                                                        <div class="plan-card {{ $index == 0 ? 'basic-plan' : ($index == 1 ? 'standard-plan featured' : 'elite-plan') }}">
                                                            <div class="plan-header">
                                                                <div class="plan-badge {{ $index == 0 ? 'basic' : ($index == 1 ? 'premium' : 'elite') }}">
                                                                    {{ $plan->name }}
                                                                </div>
                                                                <div class="plan-icon">
                                                                    <i class="fas {{ $index == 0 ? 'fa-play-circle' : ($index == 1 ? 'fa-star' : 'fa-crown') }}"></i>
                                                                </div>
                                                                @if($index == 1)
                                                                    <div class="popular-label">Popular</div>
                                                                @elseif($index == 2)
                                                                    <div class="vip-label">VIP</div>
                                                                @endif
                                                            </div>
                                                            <div class="plan-pricing">
                                                                <div class="plan-amount">${{ number_format($plan->fixed_amount, 0) }}</div>
                                                            </div>
                                                            <div class="plan-features">
                                                                <div class="feature-item">
                                                                    <i class="fas fa-video"></i>
                                                                    <span>{{ $plan->daily_video_limit == 0 ? 'Unlimited' : $plan->daily_video_limit }} Videos/Day</span>
                                                                </div>
                                                                <div class="feature-item">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                    <span>${{ number_format($plan->video_earning_rate, 4) }}/Video</span>
                                                                </div>
                                                                @if($plan->featured)
                                                                    <div class="feature-item">
                                                                        <i class="fas fa-crown"></i>
                                                                        <span>Premium Content</span>
                                                                    </div>
                                                                @endif
                                                                @if($index >= 2)
                                                                    <div class="feature-item">
                                                                        <i class="fas fa-headset"></i>
                                                                        <span>24/7 Support</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="plan-footer">
                                                                <div class="withdraw-note">
                                                                    <i class="fas fa-undo-alt"></i>
                                                                    <span>Withdraw Anytime</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="investment-notice">
                                                <div class="notice-content">
                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                    <span>Not a company investment • Just access deposit • Full control of your funds</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lottery Module -->
                                <div class="feature-card-modern" data-feature="lottery">
                                    <div class="feature-visual">
                                        <div class="lottery-showcase">
                                            <div class="lottery-header">
                                                <h5 class="text-white mb-3">
                                                    <i class="fas fa-ticket-alt me-2"></i>Weekly Lottery Draw
                                                </h5>
                                                <p class="text-white-50 mb-4">
                                                    Buy tickets • Sunday draws • Win weekly prizes
                                                </p>
                                            </div>
                                            <div class="lottery-content">
                                                <div class="lottery-ticket-display">
                                                    <div class="ticket-sample">
                                                        <div class="ticket-header">
                                                            <span class="ticket-logo">🎫</span>
                                                            <span class="ticket-title">Weekly Lottery</span>
                                                        </div>
                                                        <div class="ticket-body">
                                                            <div class="ticket-number">#LT-2025-001</div>
                                                            <div class="ticket-price">$2.00 per ticket</div>
                                                            <div class="draw-date">
                                                                <i class="fas fa-calendar text-warning"></i>
                                                                <span>Next Draw: Sunday</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="lottery-info">
                                                    <div class="info-row">
                                                        <div class="info-item">
                                                            <i class="fas fa-calendar-week text-info"></i>
                                                            <span>Every Sunday</span>
                                                        </div>
                                                        <div class="info-item">
                                                            <i class="fas fa-users text-success"></i>
                                                            <span>All Participants</span>
                                                        </div>
                                                    </div>
                                                    <div class="info-row">
                                                        <div class="info-item">
                                                            <i class="fas fa-trophy text-warning"></i>
                                                            <span>One Winner</span>
                                                        </div>
                                                        <div class="info-item">
                                                            <i class="fas fa-coins text-success"></i>
                                                            <span>Prize Pool</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lottery-notice">
                                                <div class="notice-content">
                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                    <span>Fair & transparent • Random selection • Weekly excitement</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Referral Module -->
                                <div class="feature-card-modern" data-feature="referral">
                                    <div class="feature-visual">
                                        <div class="referral-showcase">
                                            <div class="referral-header">
                                                <h5 class="text-white mb-3">
                                                    <i class="fas fa-ticket-alt me-2"></i>Referral Special Tokens
                                                </h5>
                                                <p class="text-white-50 mb-4">
                                                    Earn special tokens • Valid till Sunday • Multiple benefits
                                                </p>
                                            </div>
                                            <div class="token-display">
                                                <div class="token-card">
                                                    <div class="token-header">
                                                        <span class="token-logo">🎫</span>
                                                        <span class="token-title">Special Token</span>
                                                        <div class="token-validity">
                                                            <i class="fas fa-clock text-warning"></i>
                                                            <span>Valid till Sunday</span>
                                                        </div>
                                                    </div>
                                                    <div class="token-body">
                                                        <div class="token-count">
                                                            <span class="count-number">Access Investment Per $25/1</span>
                                                            <span class="count-label">Active Tokens</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="token-benefits">
                                                <div class="benefit-item">
                                                    <i class="fas fa-percentage text-success"></i>
                                                    <span>Give Discounts to New Users</span>
                                                </div>
                                                <div class="benefit-item">
                                                    <i class="fas fa-exchange-alt text-info"></i>
                                                    <span>Sell to Other Users</span>
                                                </div>
                                                <div class="benefit-item">
                                                    <i class="fas fa-gift text-warning"></i>
                                                    <span>Gift to Friends</span>
                                                </div>
                                                <div class="benefit-item">
                                                    <i class="fas fa-dice text-primary"></i>
                                                    <span>Auto-enter Sunday Lottery</span>
                                                </div>
                                                <div class="benefit-item">
                                                    <i class="fas fa-dollar-sign text-success"></i>
                                                    <span>Min $1 Return if No Lottery Win</span>
                                                </div>
                                            </div>
                                            <div class="token-notice">
                                                <div class="notice-content">
                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                    <span>Unused tokens automatically enter lottery • Guaranteed minimum return</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Feature Navigation -->
                            <div class="feature-navigation">
                                <button class="nav-btn active" data-target="video-earning">
                                    <i class="fas fa-video"></i>
                                    <span>Video Earning</span>
                                </button>
                                <button class="nav-btn" data-target="investment">
                                    <i class="fas fa-key"></i>
                                    <span>Video Access</span>
                                </button>
                                <button class="nav-btn" data-target="lottery">
                                    <i class="fas fa-dice"></i>
                                    <span>Lottery</span>
                                </button>
                                <button class="nav-btn" data-target="referral">
                                    <i class="fas fa-users"></i>
                                    <span>Referrals</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="badge bg-light text-dark p-3 rounded-pill">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                Trusted by {{ number_format(\App\Models\User::count()) }}+ Users Worldwide
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Gallery Section -->
    <section id="gallery" class="gallery-section">
        <div class="container">
            <div class="row">
                <div class="col-12" data-aos="fade-up">
                    <h2 class="section-title">Premium Video Gallery</h2>
                    <p class="section-subtitle">Discover amazing content and start earning money today</p>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section" data-aos="fade-up" data-aos-delay="200">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute" style="left: 20px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                            <input type="text" 
                                   class="form-control search-input ps-5" 
                                   id="searchInput" 
                                   placeholder="Search videos by title, category, or keyword...">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex justify-content-lg-end justify-content-center flex-wrap gap-2">
                            <button type="button" class="filter-btn active" data-filter="all">
                                <i class="fas fa-th-large me-2"></i>All Videos
                            </button>
                            <button type="button" class="filter-btn" data-filter="popular">
                                <i class="fas fa-fire me-2"></i>Popular
                            </button>
                            <button type="button" class="filter-btn" data-filter="recent">
                                <i class="fas fa-clock me-2"></i>Recent
                            </button>
                            <button type="button" class="filter-btn" data-filter="highest-earning">
                                <i class="fas fa-dollar-sign me-2"></i>Top Earning
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Videos Grid -->
            <div class="row" id="videosGrid">
                @include('frontend.partials.video-items', ['videos' => $videos])
            </div>

            <!-- Load More Button -->
            @if($videos->hasMorePages())
                <div class="row mt-5" data-aos="fade-up">
                    <div class="col-12 text-center">
                        <button id="loadMoreBtn" class="btn btn-primary-modern btn-modern" data-next-page="{{ $videos->currentPage() + 1 }}">
                            <span class="btn-text">
                                <i class="fas fa-plus me-2"></i>Load More Videos
                            </span>
                            <span class="btn-spinner d-none">
                                <i class="fas fa-spinner fa-spin me-2"></i>Loading...
                            </span>
                        </button>
                        <div class="mt-3">
                            <small class="text-muted">
                                Showing {{ $videos->count() }} of {{ $videos->total() }} videos
                            </small>
                        </div>
                    </div>
                </div>
            @else
                <div class="row mt-5" data-aos="fade-up">
                    <div class="col-12 text-center">
                        <div class="alert alert-info border-0 bg-light">
                            <i class="fas fa-info-circle me-2"></i>
                            You've seen all {{ $videos->total() }} available videos!
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="cta-bg-pattern"></div>
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="display-4 fw-bold mb-4">Ready to Transform Your Time into Money?</h2>
                        <p class="lead mb-5">
                            Join over 500,000 users worldwide who are already earning money daily by watching videos. 
                            Your entertainment time can now contribute to your financial goals!
                        </p>
                        
                        <div class="row mb-5">
                            <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="100">
                                <div class="text-center">
                                    <div class="display-6 fw-bold">$0.001+</div>
                                    <p class="mb-0 opacity-75">Minimum Per Video</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="200">
                                <div class="text-center">
                                    <div class="display-6 fw-bold">24/7</div>
                                    <p class="mb-0 opacity-75">Instant Withdrawals</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3" data-aos="fade-up" data-aos-delay="300">
                                <div class="text-center">
                                    <div class="display-6 fw-bold">100%</div>
                                    <p class="mb-0 opacity-75">Secure Platform</p>
                                </div>
                            </div>
                        </div>
                        
                        <div data-aos="fade-up" data-aos-delay="400">
                            <a href="{{ route('register') }}" class="btn btn-primary-modern btn-modern me-3">
                                <i class="fas fa-user-plus"></i> Create Free Account
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-secondary-modern btn-modern">
                                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title">Why Choose {{ siteName() }}?</h2>
                    <p class="section-subtitle">Experience the future of video entertainment with earning opportunities</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h4 class="feature-title">Instant Earnings</h4>
                        <p class="text-muted">
                            Earn money immediately for every video you watch. No waiting periods, no minimum thresholds - start earning from your first view!
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h4 class="feature-title">Access Deposit System</h4>
                        <p class="text-muted">
                            Small refundable deposit required for video access. Not an investment - withdraw your deposit anytime while keeping all earnings!
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="feature-title">Bank-Level Security</h4>
                        <p class="text-muted">
                            Your data and earnings are protected with military-grade encryption and advanced security protocols that banks trust.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4 class="feature-title">Lightning Fast Payouts</h4>
                        <p class="text-muted">
                            Withdraw your earnings instantly with our lightning-fast payment system supporting 20+ payment methods globally.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="feature-title">Mobile Optimized</h4>
                        <p class="text-muted">
                            Watch videos and earn money seamlessly on any device - smartphone, tablet, laptop, or desktop computer.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="feature-title">Global Community</h4>
                        <p class="text-muted">
                            Join a thriving community of over {{ number_format(\App\Models\User::count()) }}+ active users from 150+ countries who are earning daily with {{ siteName() }}.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="700">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="feature-title">Advanced Analytics</h4>
                        <p class="text-muted">
                            Track your earnings, viewing history, and progress with detailed analytics and insights to maximize your income.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                    <div class="footer-brand">
                        <img src="{{ \App\Models\GeneralSetting::getLogo() }}" alt="{{ siteName() }}" style="height: 32px; margin-right: 12px;">
                        {{ siteName() }}
                    </div>
                    <p class="text-light mb-4" style="line-height: 1.7;">
                        The world's leading video earning platform trusted by over {{ number_format(\App\Models\User::count()) }}+ users. 
                        Transform your leisure time into income and build your financial future with engaging video content.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h6 class="fw-bold mb-4" style="color: #fff; font-size: 1.1rem;">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ url('/') }}" class="text-light text-decoration-none opacity-75 hover-opacity-100">Home</a></li>
                        <li class="mb-2"><a href="{{ route('videos.public') }}" class="text-light text-decoration-none opacity-75 hover-opacity-100">Video Gallery</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-light text-decoration-none opacity-75 hover-opacity-100">Dashboard</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}" class="text-light text-decoration-none opacity-75 hover-opacity-100">Join Now</a></li>
                        <li class="mb-2"><a href="#features" class="text-light text-decoration-none opacity-75 hover-opacity-100">Features</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h6 class="fw-bold mb-4" style="color: #fff; font-size: 1.1rem;">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">Contact Support</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">FAQ</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">Video Tutorials</a></li>
                        <li class="mb-2"><a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">Community Forum</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <h6 class="fw-bold mb-4" style="color: #fff; font-size: 1.1rem;">Stay Updated</h6>
                    <p class="text-light opacity-75 mb-4">
                        Get notified about new videos, earning opportunities, and platform updates. 
                        Join our newsletter for exclusive content!
                    </p>
                    <form id="newsletterForm" class="newsletter-form mb-3">
                        <input type="email" 
                               class="newsletter-input" 
                               id="subscriberEmail" 
                               name="email" 
                               placeholder="Enter your email address..." 
                               required>
                        <button class="newsletter-btn" type="submit">
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <span class="btn-spinner d-none">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </form>
                    <div class="subscription-feedback"></div>
                    <small class="text-light opacity-50">
                        <i class="fas fa-shield-alt me-1"></i>
                        We respect your privacy. Unsubscribe anytime.
                    </small>
                </div>
            </div>
            
            <hr class="my-5" style="border-color: rgba(255, 255, 255, 0.1);">
            
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-light opacity-75 mb-0">
                        &copy; {{ date('Y') }} {{ siteName() }}. All rights reserved. 
                        <span class="d-block d-sm-inline">Made with <i class="fas fa-heart text-danger"></i> for video enthusiasts worldwide.</span>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100 me-4">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100 me-4">Terms of Service</a>
                    <a href="#" class="text-light text-decoration-none opacity-75 hover-opacity-100">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background: var(--primary-gradient); border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title text-white fw-bold" id="loginModalLabel">
                        <i class="fas fa-lock me-2"></i>Login Required
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-video text-white fa-2x"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Start Earning Money Today!</h4>
                    <p class="text-muted mb-4">
                        Join {{ siteName() }} to watch amazing videos and earn real money. 
                        Create your free account or login to access premium content.
                    </p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('register') }}" class="btn btn-gradient btn-modern">
                            <i class="fas fa-user-plus me-2"></i>Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-modern">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Business Module Showcase Functionality
        function initBusinessShowcase() {
            const navButtons = document.querySelectorAll('.nav-btn');
            const featureCards = document.querySelectorAll('.feature-card-modern');
            
            // Auto-rotate through features
            let currentFeatureIndex = 0;
            const features = ['video-earning', 'investment', 'lottery', 'referral'];
            
            function showFeature(targetFeature) {
                // Hide all feature cards
                featureCards.forEach(card => {
                    card.classList.remove('active');
                });
                
                // Remove active class from all nav buttons
                navButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Show target feature card
                const targetCard = document.querySelector(`[data-feature="${targetFeature}"]`);
                const targetBtn = document.querySelector(`[data-target="${targetFeature}"]`);
                
                if (targetCard) {
                    setTimeout(() => {
                        targetCard.classList.add('active');
                    }, 100);
                }
                
                if (targetBtn) {
                    targetBtn.classList.add('active');
                }
                
                // Start feature-specific animations
                startFeatureAnimations(targetFeature);
            }
            
            function startFeatureAnimations(feature) {
                switch(feature) {
                    case 'video-earning':
                        animateEarningsCounter();
                        break;
                    case 'investment':
                        animateInvestmentChart();
                        break;
                    case 'lottery':
                        animateLotteryWheel();
                        break;
                    case 'referral':
                        animateReferralNetwork();
                        break;
                }
            }
            
            function animateEarningsCounter() {
                const counter = document.getElementById('earningsCounter');
                if (counter) {
                    let current = 0;
                    const target = 24.87;
                    const increment = target / 100;
                    
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.textContent = current.toFixed(2);
                    }, 30);
                }
            }
            
            function animateInvestmentChart() {
                const plans = document.querySelectorAll('.feature-card-modern.active .plan-card');
                plans.forEach((plan, index) => {
                    setTimeout(() => {
                        plan.style.transform = 'scale(0.8)';
                        plan.style.opacity = '0';
                        setTimeout(() => {
                            plan.style.transition = 'all 0.6s ease-out';
                            plan.style.transform = index === 1 ? 'scale(1.05)' : 'scale(1)';
                            plan.style.opacity = '1';
                        }, 100);
                    }, index * 200);
                });
                
                // Animate the notice
                const notice = document.querySelector('.feature-card-modern.active .investment-notice');
                if (notice) {
                    setTimeout(() => {
                        notice.style.transform = 'translateY(20px)';
                        notice.style.opacity = '0';
                        setTimeout(() => {
                            notice.style.transition = 'all 0.6s ease-out';
                            notice.style.transform = 'translateY(0)';
                            notice.style.opacity = '1';
                        }, 100);
                    }, 600);
                }
            }
            
            function animateLotteryWheel() {
                const ticket = document.querySelector('.feature-card-modern.active .ticket-sample');
                if (ticket) {
                    // Animate ticket appearance
                    ticket.style.transform = 'perspective(1000px) rotateY(-15deg) scale(0.8)';
                    ticket.style.opacity = '0';
                    setTimeout(() => {
                        ticket.style.transition = 'all 0.8s ease-out';
                        ticket.style.transform = 'perspective(1000px) rotateY(-5deg) scale(1)';
                        ticket.style.opacity = '1';
                    }, 100);
                }
                
                // Animate info items
                const infoItems = document.querySelectorAll('.feature-card-modern.active .info-item');
                infoItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transform = 'translateY(20px)';
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.style.transition = 'all 0.6s ease-out';
                            item.style.transform = 'translateY(0)';
                            item.style.opacity = '1';
                        }, 100);
                    }, index * 150);
                });
                
                // Animate notice
                const notice = document.querySelector('.feature-card-modern.active .lottery-notice');
                if (notice) {
                    setTimeout(() => {
                        notice.style.transform = 'translateY(20px)';
                        notice.style.opacity = '0';
                        setTimeout(() => {
                            notice.style.transition = 'all 0.6s ease-out';
                            notice.style.transform = 'translateY(0)';
                            notice.style.opacity = '1';
                        }, 100);
                    }, 800);
                }
            }
            
            function animateReferralNetwork() {
                const nodes = document.querySelectorAll('.feature-card-modern.active .user-node');
                nodes.forEach((node, index) => {
                    node.style.animation = `slideInUp 0.6s ease-out ${index * 0.2}s forwards`;
                });
            }
            
            // Manual navigation
            navButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const target = btn.dataset.target;
                    currentFeatureIndex = features.indexOf(target);
                    showFeature(target);
                    
                    // Reset auto-rotation timer
                    clearInterval(autoRotateTimer);
                    startAutoRotation();
                });
            });
            
            // Auto-rotation
            let autoRotateTimer;
            function startAutoRotation() {
                autoRotateTimer = setInterval(() => {
                    currentFeatureIndex = (currentFeatureIndex + 1) % features.length;
                    showFeature(features[currentFeatureIndex]);
                }, 5000);
            }
            
            // Initialize with first feature
            showFeature(features[0]);
            startAutoRotation();
            
            // Pause auto-rotation on hover
            const showcase = document.querySelector('.business-showcase-container');
            if (showcase) {
                showcase.addEventListener('mouseenter', () => {
                    clearInterval(autoRotateTimer);
                });
                
                showcase.addEventListener('mouseleave', () => {
                    startAutoRotation();
                });
            }
        }
        
        // Initialize business showcase when DOM is loaded
        document.addEventListener('DOMContentLoaded', initBusinessShowcase);

        // Navbar scroll effect
        const navbar = document.getElementById('mainNavbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Lazy Loading for Video Iframes
        function initLazyLoading() {
            const placeholders = document.querySelectorAll('.iframe-placeholder');
            const videoOverlays = document.querySelectorAll('.video-overlay');
            
            // Handle placeholder clicks
            placeholders.forEach(placeholder => {
                placeholder.addEventListener('click', function() {
                    loadVideoIframe(this);
                });
            });
            
            // Handle overlay clicks
            videoOverlays.forEach(overlay => {
                overlay.addEventListener('click', function() {
                    const container = this.closest('.video-thumbnail');
                    const placeholder = container.querySelector('.iframe-placeholder');
                    if (placeholder) {
                        loadVideoIframe(placeholder);
                    }
                });
            });
            
            function loadVideoIframe(placeholder) {
                const container = placeholder.closest('.iframe-container');
                const embedUrl = container.dataset.embedUrl;
                
                if (embedUrl && !container.classList.contains('loaded')) {
                    // Show loading animation
                    placeholder.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                    
                    // Create iframe
                    const iframe = document.createElement('iframe');
                    iframe.src = embedUrl + '?rel=0&showinfo=0&controls=1&modestbranding=1&autoplay=1';
                    iframe.title = container.closest('.video-card').querySelector('.video-title').textContent;
                    iframe.frameBorder = '0';
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                    iframe.allowFullscreen = true;
                    iframe.style.width = '100%';
                    iframe.style.height = '100%';
                    iframe.style.position = 'absolute';
                    iframe.style.top = '0';
                    iframe.style.left = '0';
                    iframe.style.borderRadius = '12px';
                    
                    // Append iframe and mark as loaded
                    container.appendChild(iframe);
                    container.classList.add('loaded');
                    
                    // Hide overlay
                    const overlay = container.closest('.video-thumbnail').querySelector('.video-overlay');
                    if (overlay) {
                        overlay.style.display = 'none';
                    }
                    
                    // Fade out placeholder after iframe loads
                    iframe.onload = () => {
                        setTimeout(() => {
                            placeholder.style.opacity = '0';
                            placeholder.style.transition = 'opacity 0.3s ease';
                            setTimeout(() => {
                                placeholder.style.display = 'none';
                            }, 300);
                        }, 500);
                    };
                    
                    // Handle iframe load error
                    iframe.onerror = () => {
                        placeholder.innerHTML = `
                            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-3">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                <p class="text-muted mb-0">Failed to load video</p>
                                <small class="text-muted">Please try again later</small>
                            </div>
                        `;
                        container.classList.remove('loaded');
                    };
                }
            }
        }

        // AJAX Load More Functionality
        function initLoadMore() {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (!loadMoreBtn) return;

            loadMoreBtn.addEventListener('click', async function() {
                const btn = this;
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');
                const currentPage = parseInt(btn.dataset.nextPage);
                
                // Show loading state
                btn.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
                
                try {
                    const response = await fetch(`{{ route('videos.public') }}?page=${currentPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Failed to load more videos');
                    }
                    
                    const data = await response.json();
                    
                    // Create temporary container for new items
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = data.html;
                    
                    // Add newly-loaded class for animation
                    tempContainer.querySelectorAll('.video-item').forEach(item => {
                        item.classList.add('newly-loaded');
                    });
                    
                    // Append new videos to grid
                    const videosGrid = document.getElementById('videosGrid');
                    while (tempContainer.firstChild) {
                        videosGrid.appendChild(tempContainer.firstChild);
                    }
                    
                    // Initialize lazy loading for new items
                    initLazyLoading();
                    
                    // Re-initialize watch buttons for new items
                    initWatchButtons();
                    
                    // Update button state
                    if (data.hasMore) {
                        btn.dataset.nextPage = currentPage + 1;
                        btn.disabled = false;
                        btnText.classList.remove('d-none');
                        btnSpinner.classList.add('d-none');
                        
                        // Update counter
                        const currentCount = document.querySelectorAll('.video-item').length;
                        const counterElement = btn.parentElement.querySelector('small');
                        if (counterElement) {
                            counterElement.innerHTML = `Showing ${currentCount} videos`;
                        }
                    } else {
                        // No more videos, hide button and show completion message
                        btn.parentElement.innerHTML = `
                            <div class="alert alert-info border-0 bg-light">
                                <i class="fas fa-info-circle me-2"></i>
                                You've seen all available videos!
                            </div>
                        `;
                    }
                    
                    // Scroll to first new item
                    const firstNewItem = document.querySelector('.video-item.newly-loaded');
                    if (firstNewItem) {
                        setTimeout(() => {
                            firstNewItem.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                        }, 300);
                    }
                    
                } catch (error) {
                    console.error('Error loading more videos:', error);
                    
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-3';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Failed to load more videos. Please try again.';
                    btn.parentElement.appendChild(errorDiv);
                    
                    // Reset button
                    btn.disabled = false;
                    btnText.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');
                    
                    // Remove error message after 5 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 5000);
                }
            });
        }

        // Initialize Watch Buttons
        function initWatchButtons() {
            document.querySelectorAll('.btn-watch-earn:not(.initialized)').forEach(button => {
                button.classList.add('initialized');
                button.addEventListener('click', function() {
                    const earning = this.dataset.earning;
                    
                    // Show login modal for non-authenticated users
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    
                    // Add click animation
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        }

        // Video search functionality
        const searchInput = document.getElementById('searchInput');
        const videoItems = document.querySelectorAll('.video-item');
        const filterButtons = document.querySelectorAll('.filter-btn');

        searchInput.addEventListener('input', filterVideos);

        function filterVideos() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;

            videoItems.forEach(item => {
                const title = item.dataset.title;
                const matchesSearch = title.includes(searchTerm);
                const matchesFilter = activeFilter === 'all' || item.classList.contains(activeFilter);

                if (matchesSearch && matchesFilter) {
                    item.style.display = 'block';
                    // Add fade in animation
                    item.style.opacity = '0';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transition = 'opacity 0.3s ease';
                    }, 50);
                } else {
                    item.style.display = 'none';
                }
            });

            // Show no results message if needed
            const visibleItems = Array.from(videoItems).filter(item => item.style.display !== 'none');
            const galleryGrid = document.getElementById('videosGrid');
            
            let noResultsMsg = document.getElementById('noResultsMessage');
            if (visibleItems.length === 0 && searchTerm) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'noResultsMessage';
                    noResultsMsg.className = 'col-12 text-center py-5';
                    noResultsMsg.innerHTML = `
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No videos found</h4>
                        <p class="text-muted">Try adjusting your search terms or filters</p>
                    `;
                    galleryGrid.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        // Filter button functionality
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');
                
                // Filter videos
                filterVideos();
            });
        });

        // Watch button functionality
        document.querySelectorAll('.btn-watch-earn').forEach(button => {
            button.addEventListener('click', function() {
                const earning = this.dataset.earning;
                
                // Show login modal for non-authenticated users
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
                
                // Add click animation
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });

        // Newsletter subscription
        document.getElementById('newsletterForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('subscriberEmail').value;
            const submitBtn = this.querySelector('.newsletter-btn');
            const feedback = document.querySelector('.subscription-feedback');
            
            // Show loading state
            submitBtn.querySelector('.btn-text').style.display = 'none';
            submitBtn.querySelector('.btn-spinner').classList.remove('d-none');
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Show success message
                feedback.innerHTML = `
                    <div class="alert alert-success border-0 rounded-3 mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Successfully subscribed! Welcome to {{ siteName() }} newsletter.
                    </div>
                `;
                
                // Reset form
                this.reset();
                
            } catch (error) {
                feedback.innerHTML = `
                    <div class="alert alert-danger border-0 rounded-3 mt-3">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Subscription failed. Please try again.
                    </div>
                `;
            } finally {
                // Reset button state
                submitBtn.querySelector('.btn-text').style.display = 'inline';
                submitBtn.querySelector('.btn-spinner').classList.add('d-none');
                
                // Clear feedback after 5 seconds
                setTimeout(() => {
                    feedback.innerHTML = '';
                }, 5000);
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Lazy load videos for better performance
        const videoIframes = document.querySelectorAll('.video-thumbnail iframe');
        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (!iframe.src && iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                    }
                }
            });
        });

        videoIframes.forEach(iframe => {
            videoObserver.observe(iframe);
        });

        // Add loading states for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Remove loading states from elements
            document.querySelectorAll('.loading').forEach(el => {
                el.classList.remove('loading');
            });
            
            // Add hover effects to cards
            document.querySelectorAll('.video-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
            
            // Initialize video functionality
            initLazyLoading();
            initLoadMore();
            initWatchButtons();
        });

        // Service Worker Registration for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        // console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }

        // Add to home screen prompt for iOS
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isStandalone = window.navigator.standalone;

        if (isIOS && !isStandalone) {
            // Show iOS install instructions
            setTimeout(() => {
                const iosPrompt = document.createElement('div'); 
                iosPrompt.className = 'position-fixed bottom-0 start-0 end-0 bg-primary text-white p-3 text-center';
                iosPrompt.style.zIndex = '9999';
                iosPrompt.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-mobile-alt me-2"></i>
                            Install {{ siteName() }}: Tap <i class="fas fa-share"></i> then "Add to Home Screen"
                        </div>
                        <button class="btn btn-light btn-sm" onclick="this.parentElement.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                document.body.appendChild(iosPrompt);
                
                // Auto hide after 10 seconds
                setTimeout(() => {
                    if (iosPrompt.parentElement) {
                        iosPrompt.remove();
                    }
                }, 10000);
            }, 3000);
        }
    </script>
</body>
</html>
