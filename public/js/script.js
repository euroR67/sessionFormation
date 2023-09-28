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
        body.classList.add('dark-theme');
        nav.classList.add('dark-theme');
        footer.classList.add('dark-theme');
        bars.classList.add('dark-theme');
        logo.classList.add('dark-theme');
        profil.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        banner.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        stagiaireDetail.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        navLinks.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        formulaire.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        formInput.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        navText.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        table.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        lists.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        labels.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        sessionStagiaire.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        sessionDetail.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        sessionProgramme.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        h3.forEach(function(element) {
            element.classList.add('dark-theme');
        });
        
    }

    

    darkBtn.addEventListener('click', function() {
        // Basculer entre les modes sombre et clair
        body.classList.toggle('dark-theme');
        nav.classList.toggle('dark-theme');
        footer.classList.toggle('dark-theme');
        bars.classList.toggle('dark-theme');
        logo.classList.toggle('dark-theme');
        profil.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        banner.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        stagiaireDetail.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        formulaire.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        navText.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        formInput.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        table.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        lists.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        labels.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        sessionStagiaire.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        sessionDetail.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        sessionProgramme.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        h3.forEach(function(element) {
            element.classList.toggle('dark-theme');
        });
        

        // Enregistrer le mode sombre dans localStorage
        localStorage.setItem('darkMode', body.classList.contains('dark-theme'));
    }
    );
});