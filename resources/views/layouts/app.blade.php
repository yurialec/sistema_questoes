<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema de Questões')</title>
    
    <!-- Bootstrap 5.3 CSS (Versão mais estável da 5.3 com suporte nativo a Dark Mode) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome (Mantido para uso pontual e minimalista) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    
    <!-- Tom Select CSS (Versão específica para Bootstrap 5, herda o Dark Mode automaticamente) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
    <!-- TinyMCE (Mantido exatamente como estava para não quebrar sua configuração de upload e plugins) -->
    <script src="https://cdn.tiny.cloud/1/eajmhxz6yfnoitzg41pbo873068iyta1alws0ds67e94blgo/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '.tinymce-editor',
            height: 400,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount', 'codesample', 'paste'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | table | codesample | image | help',
            images_upload_url: '{{ route("admin.upload-imagem") }}',
            content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; }',
            paste_data_images: true, 
            codesample_languages: [
                {text: 'SQL', value: 'sql'}, {text: 'Java', value: 'java'}, {text: 'Python', value: 'python'},
                {text: 'PHP', value: 'php'}, {text: 'JavaScript', value: 'javascript'}, {text: 'HTML/XML', value: 'markup'},
                {text: 'CSS', value: 'css'}, {text: 'C#', value: 'csharp'}, {text: 'Kotlin', value: 'kotlin'},
                {text: 'Swift', value: 'swift'}, {text: 'R', value: 'r'}, {text: 'Cobol', value: 'cobol'}
            ]
        });
    </script>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</head>
<body>

    @include('components.header')
    
    <!-- Wrapper principal para garantir espaçamento limpo e organizado em todas as páginas -->
    <main class="container-fluid py-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @stack('scripts')
</body>
</html>