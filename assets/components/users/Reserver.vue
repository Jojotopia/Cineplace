<template>
    <div class="chargement" v-if="!loaded">Chargement des données en cours...</div>
    <section class="long_admin_container">
        <h3 class="form_titre">Votre commande</h3>
        
        <p id="message" v-if="message">{{message}}</p>
        <p id="error" v-if="error">{{error}}</p>
        
        <div class="div_form" v-if="films.length > 0">
            <p>
                <label for="film">Film : </label>
                <select name="film" id="film" v-model="filmChoisis">
                    <option v-for="(film,index) in films" :key="index" :value="film.id">{{film.titre}}</option>
                </select>
            </p>
            <p v-if="filmChoisis != null">
                <label for="projection">Séance : </label>
                <select name="projection" id="projection" v-model="reservation.projection">
                    <option v-for="(projection,index) in projections" :key="index" :value="projection.id">{{ new Date(projection.date).toLocaleDateString("fr-FR", {weekday: "long", year: "numeric", month: "long", day: "numeric", hour: "numeric", minute: "numeric"}).toUpperCase() }}</option>
                </select>
            </p>
            <p v-if="filmChoisis != null">
                <label for="nbPlaces">Nombre de personnes : </label>
                    <input type="number" v-model="reservation.nbSieges" @keyup.enter="reserver()" name="nbPlaces" id="nbPlaces" min="1" max="10" required>
            </p>
            <p v-if="filmChoisis != null">
                <input type="submit" @click="reserver()" value="Réserver">
            </p>
        </div>
        <p v-else class="description_film_age">Aucune projection n'est disponible !</p>
    </section>
</template>

<script setup>
    import { onMounted } from 'vue';
    import Axios from '../../api/axios'
    import { ref, computed } from 'vue'
    import { useCineplaceStore } from '../../stores/index'
    import { useRouter, useRoute } from 'vue-router'

    const store = useCineplaceStore()
    const router = useRouter()
    const route = useRoute()
    const error = ref('')
    const message = ref('')
    const allFilms = ref([])
    //films contient uniquement les films qui ont au moins une projection disponible
    const films = computed(function(){
        let filmsDispo = []
        for (const [index, unFilm] of Object.entries(allFilms.value)) {
            if( (unFilm.projections.filter(projection => new Date(projection.date) > new Date()).length > 0 )){
                filmsDispo.push(unFilm)
            }
        }
        return filmsDispo
    })
    const filmChoisis = ref(null)
    //contient les projections disponibles du film choisis
    const projections = computed(function(){
        if(filmChoisis){
            return films.value.filter(film => film.id == filmChoisis.value)[0].projections.filter(projection => new Date(projection.date) > new Date())
        }else{
            return null
        }
    })
    const reservation = ref({
        nbSieges: 1,
        client: store.login,
        projection: null,
    })
    const loaded = ref(false)

    onMounted(() => {
        Axios().get('films')
            .then(response =>  {
                allFilms.value = response.data['hydra:member'],
                loaded.value = true
            })
            .catch( e => {
                //En cas de retour négatif le chargement est effectué et aucune projection ne sera trouvé
                loaded.value = true
            })
        //Si un id à été transmis en paramêtre car on a cliqué sur le bouton réserver de la page détails
        //Alors on affiche directement toutes les information sur le film
        if(route.params && route.params.seance){
            //Demande de la projection souhaiter à l'api
            Axios().get('projections/'+route.params.seance)
                .then(response =>  {
                    reservation.value.projection = route.params.seance
                    filmChoisis.value =  response.data.film.split("/")[3]
                    loaded.value = true
                })
                .catch( e => {
                    if (e.response) {
                        if (e.response.data.message === "Expired JWT Token") {
                            error.value = 'Votre session a expirée !'
                            sessionStorage.removeItem('token')
                            setTimeout(() => router.push('/login') , 2000);
                        } else {
                            error.value = "Cette séance n'existe pas !"
                        }
                    }
                })
        }
    })

    function reserver(){
        if(reservation.value.client != null && reservation.value.projection != null){
            //Si le client demande entre 0 et 10 places ok sinon erreur
            if(reservation.value.nbSieges <= 10 && reservation.value.nbSieges > 0){
                //Demande d'ajout de la réservation via l'api
                Axios().post('reserver',reservation.value)
                    .then(response =>  {
                        error.value = ""
                        message.value = "Réservation effectué !"
                        setTimeout(() => router.push('/reservationsClient') , 2000);
                    })
                    .catch( e => {
                        if (e.response) {
                            if (e.response.data.message === "Expired JWT Token") {
                                error.value = 'Votre session a expirée !'
                                sessionStorage.removeItem('token')
                                setTimeout(() => router.push('/login') , 2000);
                            } else {
                                error.value = e.response.data['hydra:description']
                            }
                        }
                    })
            }else{
                error.value = "Le nombres de places doit être compris entre 1 et 10 !"
            }
        }else{
            error.value = "Veuillez remplir tous les champs !"
        }
    }
</script>
