<template>
  <div class="medical-container vaccine">
    <div class="medical-container-header">
      <h2>Vacunas</h2>

      <router-link v-if="vaccines.length > 0" :to="{name: vaccineListName, params: {pet_id: pet_id}}">
        Ver más
      </router-link>
    </div>

    <Placeholder
        v-if="!hasVaccines && isOwner"
        :img_src="placeholder.img_src"
        :text="placeholder.text"
        :path_name="placeholder.cta.path_name"
        :cta_text="placeholder.cta.text"
        :pet_name="pet_name"
        :pet_id="pet_id"
    />
    <p v-else-if="!hasVaccines && !isOwner">Esta mascota no tiene ninguna vacuna agregada.</p>
    <div v-else class="medical-container-body">
      <VaccineItem
          v-for="vaccine in vaccines"
          :key="vaccine.id"
          :vaccine="vaccine"
      />
    </div>
  </div>
</template>

<script>
import Placeholder from "@/components/pet/show/detail/medical/PetDetailMedicalPlaceholder";
import VaccineItem from "@/components/pet/show/detail/medical/PetDetailMedicalVaccineItem";
import store from "@/store";

export default {
  name: "PetDetailMedicalVaccine",
  components: {
    Placeholder,
    VaccineItem
  },
  props: {
    vaccines: {
      type: Array,
      required: true
    },
    placeholder: {
      type: Object,
      required: true,
    },
    pet_name: {
      type: String,
      required: true,
    },
    pet_id: {
      type: Number,
      required: true,
    }
  },
  data() {
    return {
      store,
    }
  },
  computed: {
    isOwner() {
      return !this.store.user.is_professional;
    },
    vaccineListName() {
      return this.isOwner ? 'VaccineList' : 'VaccineListShared';
    },
    hasVaccines() {
      return this.vaccines.length > 0;
    },

  }
}
</script>
