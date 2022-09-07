<template>
    <!-- Affiche d'un film -->
    <div class="affiche_film_container" @mouseenter="ouvrirResume()" @mouseleave="fermerResume()">
        <!-- Si un film n'a pas d'affiche prendre l'image par défaut-->
        <img v-if="film.affiche != null" class="affiche_film_image" :src="'http://localhost:8000/uploads/affiches/'+film.affiche" :alt="film.titre">
        <img v-else class="affiche_film_image" src="http://localhost:8000/uploads/affiches/image-par-default.jpg" :alt="film.titre">
        <p class="affiche_film_titre">{{film.titre}}</p>
        <p class="affiche_film_resume_ferme">{{resume_tronquer(film.resume)}} <br> <br> <router-link class="affiche_film_resume_button" :to="{ name: 'detailsFilm', params: { idFilm: film.id } }">Plus d'infos</router-link></p>
    </div>
</template>

<script setup>
    defineProps({
        //Le film à affiché
        film: [Object]
    })

    //Permet de limiter le résumé en nombre de caractères
    function resume_tronquer(resume){
        let longeur_max = 300;
        let texte = resume;
        if (texte.length > longeur_max){
            texte = texte.substr(0, longeur_max);
            texte += "...";
        }
        return texte;
    }
    
    //Fonctions permettant d'ouvrir et fermer le résumé
    function ouvrirResume(){
        //le résumé s'adapte à la taille de son conteneur
        event.target.querySelector(".affiche_film_resume_ferme").style.width = event.target.getBoundingClientRect().width + "px"
        event.target.querySelector(".affiche_film_resume_ferme").style.height = event.target.getBoundingClientRect().height + "px"
        event.target.querySelector(".affiche_film_resume_ferme").classList.add('affiche_film_resume_ouvert')
        event.target.querySelector(".affiche_film_resume_ferme").classList.remove('affiche_film_resume_ferme')
    }
    function fermerResume(){
        event.target.querySelector(".affiche_film_resume_ouvert").classList.add('affiche_film_resume_ferme')
        event.target.querySelector(".affiche_film_resume_ouvert").classList.remove('affiche_film_resume_ouvert')
    }

</script>