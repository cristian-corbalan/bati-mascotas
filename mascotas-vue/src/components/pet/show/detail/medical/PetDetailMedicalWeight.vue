<template>
  <div class="medical-container weight">
    <div class="medical-container-header">
      <div v-if="weights.length > 0">
        <span>Peso actual: </span>

        <span class="current-weight">{{ displayWeight(weights[0].weight) }}</span>

        <span class="weight-date"> ({{ formatDate(weights[0].date) }})</span>
      </div>

      <router-link v-if="weights.length > 0" :to="{name: weightListName, params: {pet_id: pet_id}}">Ver más</router-link>
    </div>

    <Placeholder
        v-if="!hasWeights && isOwner"
        :img_src="placeholder.img_src"
        :text="placeholder.text"
        :path_name="placeholder.cta.path_name"
        :cta_text="placeholder.cta.text"
        :pet_name="pet_name"
        :pet_id="pet_id"
    />

    <p v-else-if="!hasWeights && !isOwner">Esta mascota no tiene ningún peso agregado.</p>

    <div v-else class="medical-container-body">
      <ul>
        <li
            v-for="weight in weights.slice(1, weights.length)"
            :key="weight.id"
        >
          {{ displayWeight(weight.weight) }}

          <span class="weight-date"> ({{ formatDate(weight.date) }})</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import Placeholder from "@/components/pet/show/detail/medical/PetDetailMedicalPlaceholder";
import store from "@/store"
import {formatDate, displayWeight} from "@/helpers";

export default {
  name: "PetDetailMedicalWeight",
  components: {
    Placeholder,
  },
  props: {
    weights: {
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
      formatDate,
      displayWeight,
      store
    }
  },
  computed: {
    isOwner() {
      return !this.store.user.is_professional;
    },
    weightListName() {
      return this.isOwner ? 'WeightList' : 'WeightListShared';
    },
    hasWeights() {
      return this.weights.length > 0;
    }
  }
}
</script>
