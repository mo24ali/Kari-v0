
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARI | Minimal Luxury</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #8b5cf6;
            --background: #ffffff;
            --surface: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        
        .dark {
            --primary: #60a5fa;
            --primary-dark: #3b82f6;
            --secondary: #a78bfa;
            --background: #0f172a;
            --surface: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border: #334155;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3);
        }
        
        body {
            background-color: var(--background);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .bg-surface {
            background-color: var(--surface);
        }
        
        .border-light {
            border-color: var(--border);
        }
        
        .shadow-custom {
            box-shadow: var(--shadow);
        }
        
        .shadow-custom-lg {
            box-shadow: var(--shadow-lg);
        }
        
        .text-secondary {
            color: var(--text-secondary);
        }
        
        .hover-lift {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.7);
        }
        
        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.7);
        }
        
        .property-image {
            aspect-ratio: 16/12;
            object-fit: cover;
        }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--surface);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
        
        /* Chart colors */
        .chart-color-1 { background-color: var(--primary); }
        .chart-color-2 { background-color: var(--secondary); }
        .chart-color-3 { background-color: #10b981; }
        .chart-color-4 { background-color: #f59e0b; }
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, var(--surface) 25%, var(--border) 50%, var(--surface) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Progress bars */
        .progress-bar {
            height: 4px;
            background-color: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: var(--primary);
            border-radius: 2px;
        }
    </style>
    
</head>