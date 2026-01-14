<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />
<script src="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const { ClassicEditor, Essentials, Bold, Italic, Underline, Strikethrough, Font, 
            Paragraph, Heading, List, Link, Image, ImageToolbar, ImageCaption, ImageStyle, 
            ImageResize, MediaEmbed, Table, TableToolbar, BlockQuote, Code, CodeBlock,
            HorizontalLine, Indent, Alignment, Highlight, RemoveFormat, SourceEditing,
            GeneralHtmlSupport, AutoLink, ImageUpload, ImageInsert } = window.CKEDITOR;
    
    const textareas = document.querySelectorAll('textarea.rich-editor');
    
    textareas.forEach(function(textarea) {
        ClassicEditor
            .create(textarea, {
                plugins: [
                    Essentials, Bold, Italic, Underline, Strikethrough, Font,
                    Paragraph, Heading, List, Link, Image, ImageToolbar, ImageCaption, 
                    ImageStyle, ImageResize, MediaEmbed, Table, TableToolbar, BlockQuote, 
                    Code, CodeBlock, HorizontalLine, Indent, Alignment, Highlight, 
                    RemoveFormat, SourceEditing, GeneralHtmlSupport, AutoLink, ImageUpload, ImageInsert
                ],
                toolbar: {
                    items: [
                        'sourceEditing', '|',
                        'undo', 'redo', '|',
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'link', 'insertImage', 'mediaEmbed', 'insertTable', 'blockQuote', 'codeBlock', '|',
                        'alignment', 'bulletedList', 'numberedList', 'indent', 'outdent', '|',
                        'horizontalLine', 'highlight', 'removeFormat'
                    ],
                    shouldNotGroupWhenFull: true
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                    ]
                },
                fontSize: {
                    options: [ 10, 12, 14, 'default', 18, 20, 24, 30, 36 ]
                },
                fontFamily: {
                    options: [
                        'default',
                        'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif',
                        'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif',
                        'Verdana, Geneva, sans-serif'
                    ]
                },
                image: {
                    toolbar: [
                        'imageTextAlternative', 'toggleImageCaption', '|',
                        'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                        'resizeImage'
                    ],
                    resizeOptions: [
                        {
                            name: 'resizeImage:original',
                            label: 'Original',
                            value: null
                        },
                        {
                            name: 'resizeImage:25',
                            label: '25%',
                            value: '25'
                        },
                        {
                            name: 'resizeImage:50',
                            label: '50%',
                            value: '50'
                        },
                        {
                            name: 'resizeImage:75',
                            label: '75%',
                            value: '75'
                        }
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn', 'tableRow', 'mergeTableCells'
                    ]
                },
                mediaEmbed: {
                    previewsInData: true,
                    providers: [
                        {
                            name: 'youtube',
                            url: [
                                /^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)/,
                                /^(?:m\.)?youtube\.com\/v\/([\w-]+)/,
                                /^youtube\.com\/embed\/([\w-]+)/,
                                /^youtu\.be\/([\w-]+)/
                            ],
                            html: match => {
                                const id = match[1];
                                return (
                                    '<div style="position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;">' +
                                        `<iframe src="https://www.youtube.com/embed/${id}" ` +
                                            'style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;" ' +
                                            'frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>' +
                                        '</iframe>' +
                                    '</div>'
                                );
                            }
                        },
                        {
                            name: 'vimeo',
                            url: [
                                /^vimeo\.com\/(\d+)/,
                                /^vimeo\.com\/[^/]+\/[^/]+\/video\/(\d+)/,
                                /^vimeo\.com\/album\/[^/]+\/video\/(\d+)/,
                                /^vimeo\.com\/channels\/[^/]+\/(\d+)/,
                                /^vimeo\.com\/groups\/[^/]+\/videos\/(\d+)/,
                                /^vimeo\.com\/ondemand\/[^/]+\/(\d+)/,
                                /^player\.vimeo\.com\/video\/(\d+)/
                            ],
                            html: match => {
                                const id = match[1];
                                return (
                                    '<div style="position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;">' +
                                        `<iframe src="https://player.vimeo.com/video/${id}" ` +
                                            'style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;" ' +
                                            'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>' +
                                        '</iframe>' +
                                    '</div>'
                                );
                            }
                        }
                    ]
                },
                htmlSupport: {
                    allow: [
                        {
                            name: /^(p|h[1-6]|ul|ol|li|table|tr|td|th|thead|tbody|a|img|br|hr|strong|em|u|s|code|pre|blockquote|div|span)$/,
                            attributes: true,
                            classes: true,
                            styles: true
                        },
                        {
                            name: 'iframe',
                            attributes: ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen', 'title', 'style']
                        }
                    ],
                    disallow: [
                        { name: 'script' },
                        { name: 'style' },
                        { name: 'object' },
                        { name: 'embed' },
                        { name: 'link' },
                        { name: 'meta' }
                    ]
                },
                language: 'ro'
            })
            .then(editor => {
                textarea.ckeditorInstance = editor;
                
                const initialContent = textarea.value;
                if (initialContent) {
                    editor.setData(initialContent);
                }
                
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        textarea.value = editor.getData();
                    });
                }
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });
    });
});
</script>

<style>
    .ck-editor {
        margin-bottom: 20px;
        max-width: 100%;
        width: 100%;
    }
    
    .ck.ck-editor__main > .ck-editor__editable {
        min-height: 400px;
        max-height: 800px;
        max-width: 100%;
        overflow-x: auto;
    }
    
    .ck.ck-editor__editable_inline {
        border: 1px solid #cccccc;
        border-radius: 0 0 4px 4px;
        padding: 20px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .ck.ck-toolbar {
        border: 1px solid #cccccc;
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        background: #f5f5f5;
        max-width: 100%;
        flex-wrap: wrap;
    }
    
    .ck-content .media,
    .ck-content iframe {
        max-width: 100%;
        margin: 1em 0;
        display: block;
    }
    
    .ck-content oembed {
        max-width: 100%;
        margin: 1em 0;
    }
    
    .ck-content div[style*="position: relative"] {
        max-width: 100%;
    }
    
    .ck.ck-content p,
    .ck.ck-content h1,
    .ck.ck-content h2,
    .ck.ck-content h3,
    .ck.ck-content h4,
    .ck.ck-content li,
    .ck.ck-content span,
    .ck.ck-content div {
        color: #000000 !important;
        max-width: 100%;
        word-break: break-word;
    }

    .ck-content {
        word-break: break-word;
        overflow-wrap: break-word;
    }
</style>
