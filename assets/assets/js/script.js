document.addEventListener("DOMContentLoaded", principale);

function principale(){
    //Lorsque la page est chargée les boutons sont reliés à leur fonctions
    document.getElementById("open_menu_button").addEventListener("click", ouvrirMenu);
    document.getElementById("close_menu_button").addEventListener("click", fermerMenu);
    document.getElementById("menu").addEventListener("click", fermerMenu);

    //Fonctions permettant d'ouvrir et fermer le menu sur téléphone
    function ouvrirMenu(){
        document.getElementById("menu").classList.add('nav_ouvert');
        document.getElementById("close_menu_button").style.right = 0;
    }
    function fermerMenu(){
        document.getElementById("menu").classList.remove('nav_ouvert');
        document.getElementById("close_menu_button").style.right = "-100vw";
    }
}