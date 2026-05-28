import Quill from 'quill';
import 'quill/dist/quill.snow.css';

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.getElementById('quill-editor');
    if (!editorEl) return;

    const hiddenInput = document.getElementById('cuerpo');

    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Escribí el contenido de la noticia…',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean'],
            ],
        },
    });

    // Cargar contenido previo (en caso de error de validación)
    if (hiddenInput.value) {
        quill.root.innerHTML = hiddenInput.value;
    }

    // Sincronizar con el input hidden antes de submit
    editorEl.closest('form').addEventListener('submit', () => {
        hiddenInput.value = quill.root.innerHTML;
    });
});
