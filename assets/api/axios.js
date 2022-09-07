import axios from 'axios'
import { useCineplaceStore } from '../stores'

//Toutes les requetes axios faites dans VueJS passeront par l'api de symfony

export default() => {
    const store = useCineplaceStore()
    return axios.create({
        //window.location.origin permet de fonctionner peut importe que l'on soit 
        //sur localhost, 127.0.0.1, sur le port 8000 ou autre
        baseURL: window.location.origin + '/api/',
        withCredentials: false,
        headers: {
            'Accept': 'application/ld+json',
            'Content-Type': 'application/ld+json',
            'Authorization': 'Bearer ' + store.token
        }
    })
}
