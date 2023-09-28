// DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    const darkBtn = document.querySelector('.theme');
    const body = document.querySelector('body');
    const nav = document.querySelector('nav');
    // Regroupe dans une variable plusieurs éléments du DOM
    const navText = document.querySelectorAll('.logo-menu, header nav ul li a, .theme-text, header nav ul li');

    // Vérifiez si l'utilisateur a déjà préféré le mode sombre
    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Appliquez le mode sombre s'il est activé
    if (isDarkMode) {
        body.classList.add('dark-theme');
        nav.classList.add('dark-theme');
        navText.forEach(function(element) {
            element.classList.add('dark-theme');
        });
    }

    // Supprimez la classe de chargement initial pour afficher le contenu
    body.classList.remove('preload');

    darkBtn.addEventListener('click', function() {
        // Basculer entre les modes sombre et clair
        body.classList.toggle('dark-theme');
        nav.classList.toggle('dark-theme');
        navText.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });

        // Enregistrer le mode sombre dans localStorage
        localStorage.setItem('darkMode', body.classList.contains('dark-theme'));
    }
    );
});