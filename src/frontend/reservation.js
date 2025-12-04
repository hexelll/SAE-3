// Fonction de recherche de matériel
document.getElementById('search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const materials = document.querySelectorAll('.material-item');
    
    materials.forEach(function(material) {
        const name = material.querySelector('h3').textContent.toLowerCase();
        const category = material.querySelector('p').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || category.includes(searchTerm)) {
            material.style.display = 'grid';
        } else {
            material.style.display = 'none';
        }
    });
});

// Activer/désactiver le champ quantité selon la checkbox
document.querySelectorAll('.material-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const materialItem = this.closest('.material-item');
        const quantityInput = materialItem.querySelector('.quantity-input');
        
        if (this.checked) {
            quantityInput.required = true;
            quantityInput.focus();
            if (!quantityInput.value) {
                quantityInput.value = 1;
            }
        } else {
            quantityInput.required = false;
            quantityInput.value = '';
        }
    });
});

// Validation du formulaire
document.querySelector('.reservation-form').addEventListener('submit', function(e) {
    const checkedMaterials = document.querySelectorAll('.material-checkbox:checked');
    
    if (checkedMaterials.length === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un matériel');
        return false;
    }
    
    // Vérifier que chaque matériel sélectionné a une quantité
    let hasError = false;
    checkedMaterials.forEach(function(checkbox) {
        const materialItem = checkbox.closest('.material-item');
        const quantityInput = materialItem.querySelector('.quantity-input');
        
        if (!quantityInput.value || quantityInput.value < 1) {
            hasError = true;
            quantityInput.style.borderColor = '#dc3545';
        } else {
            quantityInput.style.borderColor = '#e0e0e0';
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert('Veuillez spécifier une quantité pour chaque matériel sélectionné');
        return false;
    }
    
    // Vérifier les dates
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    if (new Date(dateFrom) > new Date(dateTo)) {
        e.preventDefault();
        alert('La date de fin doit être après la date de début');
        return false;
    }
});