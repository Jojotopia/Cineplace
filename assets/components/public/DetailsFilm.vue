<template>
    <!-- Tant qu'axios n'a pas de réponse -->
    <div class="chargement" v-if="!loaded">Chargement des données en cours...</div>
    <div v-if="film != null" >
        <div class="description_film_container">
            <p class="description_film_titre">{{film.titre}}</p>

            <img v-if="film.affiche != null" class="description_film_image" :src="'http://localhost:8000/uploads/affiches/'+film.affiche" :alt="film.titre">
            <img v-else class="description_film_image" src="http://localhost:8000/uploads/affiches/image-par-default.jpg" :alt="film.titre">
            
            <div class="details_film_container">
                <p class="description_film_resume"><u>Résumé :</u> {{film.resume}}</p>
                <p v-if="film.restrictionAge != 0" class="description_film_age">Interdit au moins de {{film.restrictionAge}} ans.</p>
                <p v-else class="description_film_age">Tous public.</p>
            
                <p class="description_film_duree">{{ ("0"+ (new Date(film.duree).getHours())).slice(-2) }} h {{("0"+ (new Date(film.duree).getMinutes())).slice(-2)}}</p>
                <p class="description_film_date">{{ new Date(film.date_sortie).toLocaleDateString("fr-FR") }}</p>
                <p class="description_film_categorie">
                    [<span v-for="(categorie,index) in film.categorie" :key="index"><span>{{ categorie.nom }}</span> <span v-if="categorie != film.categorie[film.categorie.length-1]">, </span></span>]
                </p>
                <div class="description_film_distribution">
                    <p>Réalisateur(s) : </p>
                    <ul>
                        <li v-for="(realisateur,index) in film.realisateurs" :key="index"><span>{{realisateur.prenom}} {{realisateur.nom}}</span></li>
                    </ul>
                </div>
                <div class="description_film_distribution">
                    <p>Acteurs : </p>
                    <ul>
                        <li v-for="(acteur,index) in film.acteurs" :key="index"><span>{{acteur.prenom}} {{acteur.nom}}</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="description_film_container">
            <h3 class="description_film_titre">Projections prévues</h3>
            <div class="description_film_projection_supercontainer" v-if="projections.length != 0">
                <div class="description_film_projection_container" v-for="(projection,index) in projections" :key="index">
                    <p class="description_film_projection_date">{{ new Date(projection[0].date).toLocaleDateString("fr-FR", {weekday: "long", year: "numeric", month: "long", day: "numeric"}).toUpperCase() }}</p>
                    <div class="description_film_projection">
                        <p class="description_film_age" v-for="(seance,index2) in projection" :key="index2">
                            {{ ("0"+ (new Date(seance.date).getHours())).slice(-2) }} h {{ ("0"+ (new Date(seance.date).getMinutes())).slice(-2) }} :
                            <span v-if="seance.nbPlaceDispo > 0">{{seance.nbPlaceDispo}} places restantes. <router-link id="reservationButton" :to="{ name: 'reserver', params: { seance: seance.id } }">Réserver</router-link></span>
                            <span v-else>plus de places disponibles.</span>
                        </p>
                    </div>
                </div>
            </div>
            <div v-else>
                <p class="description_film_age">Aucune projection prévues pour ce film</p>
            </div>
        </div>
    </div>
    <!-- Si le film n'existe pas -->
    <div v-else>
        <p class="description_film_age">Ce film n'existe pas où n'a pas été trouvé !</p>
    </div>
</template>

<script setup>
    import { onMounted } from 'vue';
    import { useCineplaceStore } from '../../stores/index'
    import { ref, computed } from 'vue'
    import { useRoute } from 'vue-router'
    import Axios from '../../api/axios'

    const route = useRoute()
    let idFilm = route.params.idFilm

    const store = useCineplaceStore()
    //Permet à l'utilisateur de savoir si les informations n'existent pas 
    //ou si elles sont en train d'être chargée
    const loaded = ref(false)

    const film = ref(null)
    const projections = computed(function(){
        //Récupération des projections encore disponibles
        let projectionsDesordre = film.value.projections.filter(projection => new Date(projection.date) > new Date())
        //Classement des projections par dates
        let projectionsByDate = [];
        let datePrecedente = null;
        let i = -1;
        for (const [index, p] of Object.entries(projectionsDesordre)) {
            //Si la date de la projection est différente de celle de la précédente
            //Alors on ajoute une case a la liste
            if(datePrecedente == null || p.date.split('T')[0] != datePrecedente){
                datePrecedente = p.date.split('T')[0];
                i += 1;
                projectionsByDate.push([])
            }
            //Ajout de la projection dans la dernière case créer de la liste
            projectionsByDate[i].push(p);
        }
        return projectionsByDate;
    })

    //Au chargement récupération des données du film passée dans l'url
    onMounted(() => {
        Axios().get('films/'+idFilm)
            .then(response =>  {
                film.value = response.data,
                loaded.value = true
            })
            .catch( e => {
                //En cas de retour négatif le chargement est effectué et la page affichera non trouvé
                loaded.value = true
            })
    })

</script>