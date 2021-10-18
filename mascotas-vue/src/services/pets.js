import {API} from "../constants";

const petServices = {

    /**
     * Returns the data necessary for the creation of the add pet form
     *
     * @returns {Promise<any>}
     */
    addForm() {
        return fetch(`${API}mascotas/agregar`)
            .then(res => res.json());
    },

    /**
     * Add the pet to the database
     *
     * @param pet
     * @returns {Promise<any>}
     */
    addPet(pet) {
        return fetch(`${API}mascotas/agregar`, {
            method: 'POST',
            body: JSON.stringify(pet),
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'}
        }).then(res => res.json());
    },

    /**
     * Get all user's pets
     *
     * @returns {Promise<any>}
     */
    getOwnerPets() {
        return fetch(`${API}mascotas`, {
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'}
        }).then(res => res.json());
    }
};

export default petServices;
