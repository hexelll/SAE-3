// Gestion des onglets
function showTab(tabName) {
    // Masquer tous les contenus d'onglets
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(function(content) {
        content.classList.remove('active');
    });
    
    // Désactiver tous les boutons d'onglets
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Afficher le contenu sélectionné
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Activer le bouton correspondant
    event.target.classList.add('active');
}