<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Parkir - Sistem Manajemen Parkir">
    <title>{{ config('app.name') }} - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-inter antialiased">
    {{ $slot }}
    @livewireScripts

    <script>

    // Global Enter key handler untuk memindahkan fokus ke input selanjutnya
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            const form = e.target.form;
            if(form) {
                const focusable = Array.from(form.elements).filter(el => 
                    !el.disabled && !el.readOnly && el.type !== 'hidden' &&
                    (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'BUTTON')
                );
                const index = focusable.indexOf(e.target);
                if (index > -1 && index < focusable.length - 1) {
                    focusable[index + 1].focus();
                }
            }
        }
    });
</script>
</body>
</html>
