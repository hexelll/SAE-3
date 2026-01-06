
document.addEventListener('DOMContentLoaded', function() {
    
    
    const checkboxes = document.querySelectorAll('.material-checkbox');

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            
            const parentItem = this.closest('.material-item');
            const quantityInput = parentItem.querySelector('.quantity-input');

            if (this.checked) {
                
                quantityInput.removeAttribute('disabled');
                quantityInput.required = true;
                quantityInput.value = 1;
            } else {
               
                quantityInput.setAttribute('disabled', 'disabled');
                quantityInput.required = false;
                quantityInput.value = '';
            }
        });
    });

   
    const searchInput = document.getElementById('search');
    if(searchInput){
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.material-item');

            items.forEach(function(item) {
                const name = item.querySelector('h3').textContent.toLowerCase();
                
            });
        });
    }
});