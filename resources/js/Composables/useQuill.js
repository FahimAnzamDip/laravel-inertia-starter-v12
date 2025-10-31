import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { onMounted, ref } from 'vue';

// Register custom size style (px values) for font size support
const SizeStyle = Quill.import('attributors/style/size');
SizeStyle.whitelist = ['12px', '14px', '16px', '18px', '24px', '32px'];
Quill.register(SizeStyle, true);

export function useQuill({ containerRef, model, toolbar = null, formats = null } = {}) {
    const quill = ref(null);

    const defaultToolbar = [
        [{ header: [false] }],
        // [{ size: ['12px', '14px', '16px', '18px', '24px', '32px'] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link'], // image
        ['clean'],
    ];

    const defaultFormats = ['header', 'size', 'bold', 'italic', 'underline', 'strike', 'list', 'link', 'image'];

    onMounted(() => {
        if (!containerRef || !containerRef.value) {
            console.warn('useQuill: containerRef is not provided or not mounted yet');
            return;
        }

        quill.value = new Quill(containerRef.value, {
            theme: 'snow',
            modules: {
                toolbar: toolbar || defaultToolbar,
            },
            formats: formats || defaultFormats,
        });

        // Load initial content if available
        if (model && model.value) {
            quill.value.root.innerHTML = model.value;
        }

        // Sync editor -> model
        if (model) {
            quill.value.on('text-change', () => {
                model.value = quill.value.root.innerHTML;
            });
        }
    });

    function setContents(html) {
        if (quill.value) {
            quill.value.root.innerHTML = html || '';
        }
    }

    return { quill, setContents };
}
