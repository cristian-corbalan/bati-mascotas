<template>
  <TheLoader v-if="loading"/>

  <BaseFormContainer
      v-else
      :is_short_form="false"
      headline="Editar mascota"
      form_class="form-pet-edit"
  >
    <PetForm
        v-if="pet"
        :sexes="sexes"
        :species="species"
        :pet="pet"
    />
  </BaseFormContainer>
</template>

<script>
import BaseFormContainer from "@/components/general/form/BaseFormContainer";
import PetForm from "@/components/pet/PetForm";
import petServices from "@/services/pets";
import store from "@/store";
import TheLoader from "@/components/general/layout/TheLoader";
import {handleAccessError, handleAuthenticationError} from "@/helpers";

export default {
  name: "PetEditForm",
  components: {
    TheLoader,
    PetForm,
    BaseFormContainer,
  },
  data: () => ({
    loading: true,
    sexes: [],
    species: [],
    pet: null,
    handleAccessError,
    handleAuthenticationError,
    store,
  }),
  mounted() {
    petServices.createForm()
        .then(res => {
          if (this.handleAuthenticationError(res)) return;

          this.sexes = res.data.sexes;

          this.species = res.data.species;
        });

    petServices.find(this.$route.params.pet_id)
        .then(res => {
          if (res.data) {
            this.pet = res.data.pet;
          } else {
            if (this.handleAccessError(res)) return;

            this.store.setStatus({
              msg: "No se encontró la mascota solicitada.",
              type: 'Error'
            });

            this.$router.push({name: 'Pets'});
          }
          this.loading = false;
        })
  }
}
</script>
