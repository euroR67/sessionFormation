document.addEventListener('DOMContentLoaded', function() {
    const darkBtn = document.querySelector('.theme');
    const body = document.querySelector('body');
    const nav = document.querySelector('nav');
    const navLinks = document.querySelectorAll('header nav ul li');
    const footer = document.querySelector('footer');
    const formulaire = document.querySelectorAll('.container');
    const formInput = document.querySelectorAll('.form-control');
    const banner = document.querySelectorAll('.banner');
    const table = document.querySelectorAll('main table tbody tr');
    const lists = document.querySelectorAll('.wrapper div .lists li a');
    const labels = document.querySelectorAll('main table tbody tr td p');
    const sessionDetail = document.querySelectorAll('.session-detail');
    const sessionProgramme = document.querySelectorAll('.programme');
    const sessionStagiaire = document.querySelectorAll('.stagiaire, .combined');
    const bars = document.querySelector('.barsIcon');
    const h3 = document.querySelectorAll('.wrapper h3');
    const logo = document.querySelector('header .logo');
    const profil = document.querySelectorAll('header .user a');
    const stagiaireDetail = document.querySelectorAll('.fiche-stagiaire');
    // Regroupe dans une variable plusieurs éléments du DOM
    const navText = document.querySelectorAll('.logo-menu, header nav ul li a, .theme-text, header nav ul li');
    // Supprimez la classe de chargement initial pour afficher le contenu
    body.classList.remove('preload');
    // Vérifiez si l'utilisateur a déjà préféré le mode sombre
    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Appliquez le mode sombre s'il est activé
    if (isDarkMode) {
        const darkElements = [body, nav, footer, bars, logo, ...profil, ...banner, ...stagiaireDetail, ...navLinks, ...formulaire, ...formInput, ...navText, ...table, ...lists, ...labels, ...sessionStagiaire, ...sessionDetail, ...sessionProgramme, ...h3];
        darkElements.forEach(function(element) {
            element.classList.add('dark-theme');
        });
    }

    darkBtn.addEventListener('click', function() {
        // Basculer entre les modes sombre et clair
        const toggleElements = [body, nav, footer, bars, logo, ...profil, ...banner, ...stagiaireDetail, ...formulaire, ...navText, ...formInput, ...table, ...lists, ...labels, ...sessionStagiaire, ...sessionDetail, ...sessionProgramme, ...h3];
        toggleElements.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });

        // Enregistrer le mode sombre dans localStorage
        localStorage.setItem('darkMode', body.classList.contains('dark-theme'));
    });
});