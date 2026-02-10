// Form validasyonu
document.querySelectorAll('form').forEach(form => {
    // Eğer form'un kendi submit handler'ı varsa (data-custom-validation), bu validasyonu atla
    if (form.dataset.customValidation === 'true') {
        return;
    }
    
    form.addEventListener('submit', (e) => {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            // File input kontrolü
            if (field.type === 'file') {
                if (!field.files || field.files.length === 0) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '#e2e8f0';
                }
            } else {
                // Normal input kontrolü
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '#e2e8f0';
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Lütfen tüm gerekli alanları doldurun!');
        }
    });
});

// Slug otomatik oluştur
const titleInput = document.querySelector('input[name="title"]');
const slugInput = document.querySelector('input[name="slug"]');

if (titleInput && slugInput) {
    titleInput.addEventListener('input', (e) => {
        if (!slugInput.dataset.manuallyEdited) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/ı/g, 'i')
                .replace(/ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', () => {
        slugInput.dataset.manuallyEdited = 'true';
    });
}
