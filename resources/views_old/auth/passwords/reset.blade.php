@extends('layouts.form_layout')

@section('content') 
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

    /* Reset body and html to remove any default margins/padding */
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        -webkit-text-size-adjust: 100% !important;
        -ms-text-size-adjust: 100% !important;
    }

    *, *::before, *::after {
        box-sizing: border-box !important;
    }

    /* Prevent iOS zoom on form inputs */
    input[type="text"], input[type="email"], input[type="password"] {
        font-size: 16px !important;
    }

    .stunning-reset-page {
        height: 100vh;
        width: 100vw;
        background: linear-gradient(135deg, 
            #667eea 0%, 
            #764ba2 20%, 
            #f093fb 40%, 
            #f5576c 60%, 
            #4facfe 80%,
            #00f2fe 100%);
        background-size: 600% 600%;
        animation: gradientShift 15s ease infinite;
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
        z-index: 9999;
    }

    .stunning-reset-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.4;
        pointer-events: none;
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        25% { background-position: 100% 50%; }
        50% { background-position: 100% 100%; }
        75% { background-position: 50% 100%; }
        100% { background-position: 0% 50%; }
    }

    .stunning-reset-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        width: 100vw;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 1;
        overflow: hidden;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
    }

    .stunning-reset-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(25px) saturate(200%);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 28px;
        padding: 50px 45px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.15),
            0 0 100px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.25),
            inset 0 -1px 0 rgba(0, 0, 0, 0.1);
        transform: translateY(0) scale(1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        animation: cardEntrance 1s cubic-bezier(0.4, 0, 0.2, 1);
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
    }

    /* Custom scrollbar for the card */
    .stunning-reset-card::-webkit-scrollbar {
        width: 6px;
    }

    .stunning-reset-card::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .stunning-reset-card::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }

    .stunning-reset-card::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    @keyframes cardEntrance {
        from {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .stunning-reset-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.8), 
            rgba(102, 126, 234, 0.8),
            rgba(255, 255, 255, 0.8),
            transparent);
        animation: shimmer 3s ease-in-out infinite;
        border-radius: 28px 28px 0 0;
    }

    .stunning-reset-card::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
        transform: translate(-50%, -50%);
        animation: pulse 4s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        50% { left: 100%; }
        100% { left: 100%; }
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.05); }
    }

    .stunning-reset-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 
            0 35px 70px rgba(0, 0, 0, 0.2),
            0 0 120px rgba(255, 255, 255, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.35),
            inset 0 -1px 0 rgba(0, 0, 0, 0.1);
    }

    .stunning-header {
        text-align: center;
        margin-bottom: 45px;
        position: relative;
        z-index: 2;
    }

    .stunning-logo {
        width: 90px;
        height: 90px;
        margin: 0 auto 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 
            0 15px 30px rgba(102, 126, 234, 0.4),
            0 0 60px rgba(118, 75, 162, 0.3),
            inset 0 2px 0 rgba(255, 255, 255, 0.3);
        animation: logoFloat 3s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }

    .stunning-logo::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: logoShine 4s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        25% { transform: translateY(-5px) rotate(1deg); }
        50% { transform: translateY(0) rotate(0deg); }
        75% { transform: translateY(-3px) rotate(-1deg); }
    }

    @keyframes logoShine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        50% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .stunning-logo i {
        font-size: 38px;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 1;
        position: relative;
    }

    .stunning-title {
        color: white;
        font-size: 36px;
        font-weight: 800;
        margin: 0 0 12px 0;
        text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        letter-spacing: -0.8px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .stunning-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 17px;
        font-weight: 400;
        margin: 0 0 25px 0;
        line-height: 1.6;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .stunning-form-group {
        margin-bottom: 28px;
        position: relative;
        z-index: 2;
    }

    .stunning-input-container {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
    }

    .stunning-input-container:focus-within {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 
            0 0 0 4px rgba(255, 255, 255, 0.1),
            0 10px 30px rgba(0, 0, 0, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .stunning-input-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, 
            #667eea 0%, 
            #764ba2 25%, 
            #f093fb 50%, 
            #f5576c 75%, 
            #4facfe 100%);
        transform: scaleX(0);
        transition: transform 0.4s ease;
        z-index: 1;
        border-radius: 18px 18px 0 0;
    }

    .stunning-input-container:focus-within::before {
        transform: scaleX(1);
    }

    .stunning-input-icon {
        position: absolute;
        left: 22px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
        font-size: 22px;
        transition: all 0.4s ease;
        z-index: 2;
    }

    .stunning-input-container:focus-within .stunning-input-icon {
        color: white;
        transform: translateY(-50%) scale(1.15);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .stunning-input {
        width: 100%;
        padding: 22px 22px 22px 65px;
        border: none;
        background: transparent;
        color: white;
        font-size: 17px;
        font-weight: 500;
        outline: none;
        transition: all 0.4s ease;
        z-index: 2;
        position: relative;
        line-height: 1.5;
    }

    .stunning-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
        transition: all 0.4s ease;
    }

    .stunning-input:focus::placeholder {
        opacity: 0;
        transform: translateY(-15px);
    }

    .stunning-floating-label {
        position: absolute;
        left: 65px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 17px;
        font-weight: 500;
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 3;
        background: transparent;
    }

    .stunning-input:focus + .stunning-floating-label,
    .stunning-input:not(:placeholder-shown) + .stunning-floating-label {
        top: 10px;
        left: 18px;
        font-size: 13px;
        color: white;
        font-weight: 600;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: none;
    }

    .stunning-password-toggle {
        position: absolute;
        right: 22px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        font-size: 22px;
        cursor: pointer;
        transition: all 0.4s ease;
        z-index: 3;
        padding: 8px;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stunning-password-toggle:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    }

    .stunning-error-message {
        color: #ff6b6b;
        font-size: 15px;
        margin-top: 10px;
        font-weight: 500;
        opacity: 0;
        animation: slideInError 0.4s ease forwards;
        background: rgba(255, 107, 107, 0.1);
        padding: 8px 12px;
        border-radius: 8px;
        border-left: 3px solid #ff6b6b;
    }

    @keyframes slideInError {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stunning-submit-btn {
        width: 100%;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
        background-size: 200% 200%;
        border: none;
        border-radius: 18px;
        color: white;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 15px 30px rgba(102, 126, 234, 0.4),
            0 0 60px rgba(118, 75, 162, 0.2);
        margin: 30px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        z-index: 2;
    }

    .stunning-submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255, 255, 255, 0.3), 
            rgba(255, 255, 255, 0.1),
            transparent);
        transition: left 0.6s;
    }

    .stunning-submit-btn:hover::before {
        left: 100%;
    }

    .stunning-submit-btn:hover {
        transform: translateY(-4px) scale(1.02);
        background-position: 100% 100%;
        box-shadow: 
            0 20px 40px rgba(102, 126, 234, 0.5),
            0 0 80px rgba(118, 75, 162, 0.3),
            inset 0 2px 0 rgba(255, 255, 255, 0.2);
        animation: buttonPulse 2s ease-in-out infinite;
    }

    .stunning-submit-btn:active {
        transform: translateY(-2px) scale(1.01);
    }

    @keyframes buttonPulse {
        0%, 100% { box-shadow: 0 20px 40px rgba(102, 126, 234, 0.5), 0 0 80px rgba(118, 75, 162, 0.3); }
        50% { box-shadow: 0 25px 50px rgba(102, 126, 234, 0.6), 0 0 100px rgba(118, 75, 162, 0.4); }
    }

    .stunning-footer-links {
        text-align: center;
        margin-top: 30px;
        position: relative;
        z-index: 2;
    }

    .stunning-footer-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 15px;
        margin-bottom: 15px;
        font-weight: 400;
    }

    .stunning-footer-link {
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.4s ease;
        padding: 2px 4px;
        border-radius: 4px;
        background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.05) 100%);
    }

    .stunning-footer-link:hover {
        border-bottom-color: white;
        color: white;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
    }

    /* Enhanced Responsive Design */
    @media (max-width: 768px) {
        .stunning-reset-container {
            padding: 10px;
        }
        
        .stunning-reset-card {
            width: 95%;
            max-width: none;
            padding: 35px 25px;
            border-radius: 24px;
            max-height: 95vh;
        }
        
        .stunning-title {
            font-size: 30px;
        }
        
        .stunning-subtitle {
            font-size: 16px;
        }
        
        .stunning-logo {
            width: 75px;
            height: 75px;
        }
        
        .stunning-logo i {
            font-size: 32px;
        }
        
        .stunning-input {
            padding: 18px 18px 18px 55px;
            font-size: 16px;
        }
        
        .stunning-floating-label {
            left: 55px;
            font-size: 16px;
        }
        
        .stunning-input:focus + .stunning-floating-label,
        .stunning-input:not(:placeholder-shown) + .stunning-floating-label {
            left: 15px;
            font-size: 12px;
        }
        
        .stunning-input-icon {
            left: 18px;
            font-size: 20px;
        }
        
        .stunning-password-toggle {
            right: 18px;
            font-size: 20px;
            width: 40px;
            height: 40px;
        }
        
        .stunning-submit-btn {
            font-size: 16px;
            padding: 18px;
        }
    }

    @media (max-width: 480px) {
        .stunning-reset-container {
            padding: 5px;
        }
        
        .stunning-reset-card {
            width: 98%;
            padding: 30px 20px;
            border-radius: 20px;
            max-height: 98vh;
        }
        
        .stunning-title {
            font-size: 26px;
        }
        
        .stunning-subtitle {
            font-size: 15px;
        }
        
        .stunning-logo {
            width: 70px;
            height: 70px;
            margin-bottom: 20px;
        }
        
        .stunning-logo i {
            font-size: 28px;
        }
        
        .stunning-form-group {
            margin-bottom: 24px;
        }
        
        .stunning-input {
            padding: 16px 16px 16px 50px;
            font-size: 15px;
        }
        
        .stunning-floating-label {
            left: 50px;
            font-size: 15px;
        }
        
        .stunning-input:focus + .stunning-floating-label,
        .stunning-input:not(:placeholder-shown) + .stunning-floating-label {
            left: 12px;
            font-size: 11px;
        }
        
        .stunning-input-icon {
            left: 16px;
            font-size: 18px;
        }
        
        .stunning-password-toggle {
            right: 16px;
            font-size: 18px;
            width: 36px;
            height: 36px;
        }
        
        .stunning-submit-btn {
            font-size: 15px;
            padding: 16px;
            margin: 25px 0;
        }
        
        .stunning-error-message {
            font-size: 14px;
            padding: 6px 10px;
        }
    }

    @media (max-width: 360px) {
        .stunning-reset-container {
            padding: 2px;
        }
        
        .stunning-reset-card {
            width: 99%;
            padding: 25px 15px;
            max-height: 99vh;
            border-radius: 16px;
        }
        
        .stunning-title {
            font-size: 24px;
        }
        
        .stunning-subtitle {
            font-size: 14px;
        }
        
        .stunning-input {
            padding: 14px 14px 14px 45px;
        }
        
        .stunning-floating-label {
            left: 45px;
        }
        
        .stunning-input-icon {
            left: 14px;
        }
        
        .stunning-password-toggle {
            right: 14px;
        }
        
        .stunning-submit-btn {
            padding: 16px;
            font-size: 15px;
        }
    }

    /* Extra small devices (iPhone SE, Galaxy S5, etc.) */
    @media (max-width: 320px) {
        .stunning-reset-page {
            overflow: auto;
            height: 100vh;
            min-height: 100vh;
        }
        
        .stunning-reset-container {
            padding: 1px;
            min-height: 100vh;
            overflow: auto;
        }
        
        .stunning-reset-card {
            width: 100%;
            padding: 20px 12px;
            max-height: none;
            min-height: auto;
            border-radius: 12px;
            margin: 2px 0;
        }
        
        .stunning-title {
            font-size: 22px;
            margin-bottom: 8px;
        }
        
        .stunning-subtitle {
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        .stunning-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
        }
        
        .stunning-logo i {
            font-size: 24px;
        }
        
        .stunning-form-group {
            margin-bottom: 20px;
        }
        
        .stunning-input {
            padding: 12px 12px 12px 40px;
            font-size: 15px;
        }
        
        .stunning-floating-label {
            left: 40px;
            font-size: 14px;
        }
        
        .stunning-input-icon {
            left: 12px;
            font-size: 18px;
        }
        
        .stunning-password-toggle {
            right: 12px;
            font-size: 18px;
            width: 35px;
            height: 35px;
        }
        
        .stunning-submit-btn {
            padding: 15px;
            font-size: 14px;
        }
    }

    /* Landscape mode for mobile devices */
    @media (max-height: 600px) and (orientation: landscape) {
        .stunning-reset-card {
            max-height: 95vh;
            padding: 20px;
        }
        
        .stunning-title {
            font-size: 24px;
        }
        
        .stunning-subtitle {
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .stunning-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
        }
        
        .stunning-logo i {
            font-size: 24px;
        }
        
        .stunning-form-group {
            margin-bottom: 20px;
        }
        
        .stunning-input {
            padding: 14px 14px 14px 45px;
        }
        
        .stunning-submit-btn {
            padding: 14px;
            margin: 20px 0;
        }
    }

    /* iOS specific fixes */
    @supports (-webkit-appearance: none) and (not (display: grid)) {
        .stunning-reset-page {
            height: 100vh;
            height: calc(var(--vh, 1vh) * 100);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        .stunning-reset-container {
            height: 100vh;
            height: calc(var(--vh, 1vh) * 100);
        }
        
        .stunning-reset-card {
            max-height: 95vh;
            max-height: calc(var(--vh, 1vh) * 95);
        }
    }

    /* Dark mode improvements */
    @media (prefers-color-scheme: dark) {
        .stunning-reset-card {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(30px) saturate(150%);
        }
        
        .stunning-input-container {
            background: rgba(0, 0, 0, 0.1);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .stunning-input-container:focus-within {
            background: rgba(0, 0, 0, 0.15);
        }
    }

    /* High contrast mode */
    @media (prefers-contrast: high) {
        .stunning-reset-card {
            border-width: 2px;
        }
        
        .stunning-input-container {
            border-width: 2px;
        }
        
        .stunning-submit-btn {
            border: 2px solid rgba(255, 255, 255, 0.5);
        }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        
        .stunning-reset-page {
            animation: none;
        }
        
        .stunning-reset-page::before {
            animation: none;
        }
        
        .stunning-logo {
            animation: none;
        }
        
        .stunning-reset-card::before,
        .stunning-reset-card::after {
            animation: none;
        }
    }
</style>

<div class="stunning-reset-page">
    <div class="stunning-reset-container">
        <div class="stunning-reset-card">
            <div class="stunning-header">
                <div class="stunning-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="stunning-title">{{ __('Reset Password') }}</h1>
                <p class="stunning-subtitle">{{ __('Enter your new password below to reset your account password.') }}</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" id="stunningResetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-envelope"></i>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="stunning-input" 
                               placeholder=" "
                               value="{{ $email ?? old('email') }}"
                               autocomplete="email"
                               readonly
                               required>
                        <label for="email" class="stunning-floating-label">{{ __('Email Address') }}</label>
                    </div>
                    @error('email')
                        <div class="stunning-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-lock"></i>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="stunning-input" 
                               placeholder=" "
                               autocomplete="new-password"
                               minlength="8"
                               required>
                        <label for="password" class="stunning-floating-label">{{ __('New Password') }}</label>
                        <button type="button" class="stunning-password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="stunning-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="stunning-form-group">
                    <div class="stunning-input-container">
                        <i class="stunning-input-icon fas fa-lock"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="stunning-input" 
                               placeholder=" "
                               autocomplete="new-password"
                               required>
                        <label for="password_confirmation" class="stunning-floating-label">{{ __('Confirm New Password') }}</label>
                        <button type="button" class="stunning-password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="passwordConfirmToggleIcon"></i>
                        </button>
                    </div>
                    <div id="passwordMatch" class="stunning-error-message" style="display: none;"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="stunning-submit-btn" id="submitBtn">
                    <span>{{ __('Reset Password') }}</span>
                </button>

                <!-- Footer Links -->
                <div class="stunning-footer-links">
                    <p class="stunning-footer-text">{{ __('Remember your password?') }}</p>
                    <a href="{{ route('login') }}" class="stunning-footer-link">{{ __('Back to Login') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix for mobile viewport height issues (iOS Safari)
    function setViewportHeight() {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setViewportHeight();
    window.addEventListener('resize', setViewportHeight);
    window.addEventListener('orientationchange', function() {
        setTimeout(setViewportHeight, 100);
    });
    
    // Enhanced password toggle functionality with smooth transitions
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = inputId === 'password' ? 
            document.getElementById('passwordToggleIcon') : 
            document.getElementById('passwordConfirmToggleIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.style.transform = 'scale(1.1)';
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.style.transform = 'scale(1)';
        }
        
        // Add a small animation effect
        setTimeout(() => {
            icon.style.transform = '';
        }, 200);
    };

    // Enhanced form submission with loading state
    const form = document.getElementById('stunningResetForm');
    const submitBtn = document.getElementById('submitBtn');
    const originalBtnText = submitBtn.innerHTML;

    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting Password...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
        
        // Add visual feedback to the form
        form.style.pointerEvents = 'none';
        form.style.opacity = '0.8';
    });

    // Enhanced password match checker with real-time validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatchDiv = document.getElementById('passwordMatch');

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword.length === 0) {
            passwordMatchDiv.style.display = 'none';
            confirmPasswordInput.parentElement.style.borderColor = '';
            return;
        }

        if (password === confirmPassword) {
            passwordMatchDiv.style.display = 'none';
            confirmPasswordInput.parentElement.style.borderColor = 'rgba(108, 207, 127, 0.6)';
            confirmPasswordInput.parentElement.style.backgroundColor = 'rgba(108, 207, 127, 0.05)';
            
            // Add success icon
            let successIcon = confirmPasswordInput.parentElement.querySelector('.success-icon');
            if (!successIcon) {
                successIcon = document.createElement('i');
                successIcon.className = 'fas fa-check-circle success-icon';
                successIcon.style.cssText = `
                    position: absolute;
                    right: 60px;
                    top: 50%;
                    transform: translateY(-50%);
                    color: rgba(108, 207, 127, 0.8);
                    font-size: 18px;
                    z-index: 4;
                `;
                confirmPasswordInput.parentElement.appendChild(successIcon);
            }
        } else {
            passwordMatchDiv.style.display = 'block';
            passwordMatchDiv.textContent = 'Passwords do not match';
            confirmPasswordInput.parentElement.style.borderColor = 'rgba(255, 107, 107, 0.6)';
            confirmPasswordInput.parentElement.style.backgroundColor = 'rgba(255, 107, 107, 0.05)';
            
            // Remove success icon if it exists
            const successIcon = confirmPasswordInput.parentElement.querySelector('.success-icon');
            if (successIcon) {
                successIcon.remove();
            }
        }
    }

    // Password strength indicator
    function checkPasswordStrength(password) {
        const strengthIndicators = {
            weak: { color: '#ff6b6b', text: 'Weak' },
            fair: { color: '#ffa726', text: 'Fair' },
            good: { color: '#66bb6a', text: 'Good' },
            strong: { color: '#4caf50', text: 'Strong' }
        };

        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;

        let strength = 'weak';
        if (score >= 4) strength = 'strong';
        else if (score >= 3) strength = 'good';
        else if (score >= 2) strength = 'fair';

        return strengthIndicators[strength];
    }

    // Add password strength indicator
    passwordInput.addEventListener('input', function() {
        checkPasswordMatch();
        
        const strength = checkPasswordStrength(this.value);
        let strengthIndicator = passwordInput.parentElement.querySelector('.strength-indicator');
        
        if (this.value.length > 0) {
            if (!strengthIndicator) {
                strengthIndicator = document.createElement('div');
                strengthIndicator.className = 'strength-indicator';
                strengthIndicator.style.cssText = `
                    position: absolute;
                    right: 60px;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 12px;
                    font-weight: 600;
                    padding: 2px 6px;
                    border-radius: 4px;
                    z-index: 4;
                    transition: all 0.3s ease;
                `;
                passwordInput.parentElement.appendChild(strengthIndicator);
            }
            
            strengthIndicator.textContent = strength.text;
            strengthIndicator.style.color = strength.color;
            strengthIndicator.style.backgroundColor = strength.color + '20';
            strengthIndicator.style.border = `1px solid ${strength.color}40`;
        } else if (strengthIndicator) {
            strengthIndicator.remove();
        }
    });

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Enhanced input focus animations with floating label improvements
    const inputs = document.querySelectorAll('.stunning-input');
    inputs.forEach(input => {
        // Mobile input handling to prevent iOS zoom
        input.addEventListener('touchstart', function() {
            if (window.innerWidth <= 768) {
                const viewport = document.querySelector('meta[name="viewport"]');
                if (viewport) {
                    viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                }
            }
        });
        
        // Focus event
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            this.parentElement.style.transform = 'translateY(-3px)';
            
            // Prevent iOS from scrolling the page when focusing
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        });

        // Blur event
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
                this.parentElement.style.transform = '';
            } else {
                this.parentElement.style.transform = '';
            }
        });

        // Input event for real-time validation
        input.addEventListener('input', function() {
            // Add typing animation to icon
            const icon = this.parentElement.querySelector('.stunning-input-icon');
            if (icon) {
                icon.style.transform = 'translateY(-50%) scale(1.1)';
                setTimeout(() => {
                    icon.style.transform = 'translateY(-50%) scale(1)';
                }, 150);
            }
        });

        // Initial check for pre-filled values
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });

    // Add smooth scroll behavior for better UX
    if ('scrollBehavior' in document.documentElement.style) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }

    // Add keyboard navigation improvements
    document.addEventListener('keydown', function(e) {
        // Enter key on inputs should move to next input or submit
        if (e.key === 'Enter' && e.target.classList.contains('stunning-input')) {
            e.preventDefault();
            const inputs = Array.from(document.querySelectorAll('.stunning-input'));
            const currentIndex = inputs.indexOf(e.target);
            
            if (currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            } else {
                submitBtn.click();
            }
        }
    });

    // Add visual feedback for form validation
    form.addEventListener('invalid', function(e) {
        e.preventDefault();
        const firstInvalidField = form.querySelector(':invalid');
        if (firstInvalidField) {
            firstInvalidField.focus();
            firstInvalidField.parentElement.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                firstInvalidField.parentElement.style.animation = '';
            }, 500);
        }
    }, true);

    // Add shake animation for validation errors
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);

    // Add success animation for successful form submission
    if (window.location.search.includes('success')) {
        const card = document.querySelector('.stunning-reset-card');
        card.style.animation = 'successPulse 1s ease-in-out';
        
        const successStyle = document.createElement('style');
        successStyle.textContent = `
            @keyframes successPulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); box-shadow: 0 0 50px rgba(108, 207, 127, 0.5); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(successStyle);
    }
});
</script>
@endsection
