<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    
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
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            paste_data_images: true, 
            
            codesample_languages: [
                {text: 'SQL', value: 'sql'},
                {text: 'Java', value: 'java'},
                {text: 'Python', value: 'python'},
                {text: 'PHP', value: 'php'},
                {text: 'JavaScript', value: 'javascript'},
                {text: 'HTML/XML', value: 'markup'},
                {text: 'CSS', value: 'css'},
                {text: 'C#', value: 'csharp'},
                {text: 'Kotlin', value: 'kotlin'},
                {text: 'Swift', value: 'swift'},
                {text: 'R', value: 'r'},
                {text: 'Cobol', value: 'cobol'}
            ]
        });
    </script>
</head>
<body>

    @include('components.header')
    @yield('content')

    <!-- Bootstrap 5 JS (inclui Popper.js para componentes interativos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>