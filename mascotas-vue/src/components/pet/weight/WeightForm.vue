<template>
  <v-form
      ref="weightForm"
      :action="formAction"
      method="post"
      @submit.prevent="sendForm"
  >
    <InputText
        v-model="formData.weight"
        :loading="loading"
        :rules="[rules.obligatory, rules.numeric]"
        :errors="errors.weight"
        label="Peso en gramos"
        identifier="weight"
        type="number"
        required
    />

    <InputDate
        :loading="loading"
        :rules="[rules.obligatory, rules.date]"
        :errors="errors.date"
        :initialDate="initialDate"
        :maxDate="getCurrentDate()"
        label="Fecha"
        identifier="date"
        @update-date="updateDate"
    />

    <button class="main-btn" type="submit" :disabled="loading">{{ buttonText }}</button>
  </v-form>
</template>

<script>
import InputText from "@/components/general/input/InputText";
import InputDate from "@/components/general/input/InputDate";
import store from "@/store";
import weightService from "@/services/weights";
import {handleAccessError} from "@/helpers";

export default {
  name: "WeightForm",
  components: {
    InputText,
    InputDate
  },
  props: {
    pet_id: {
      type: [String, Number],
      required: true,
    },
    weight: {
      type: Object,
      default: null,
    },
  },
  data: function () {
    return {
      loading: false,
      store,
      handleAccessError,
      formData: {
        weight: null,
        date: this.getCurrentDate(),
        pet_id: this.pet_id
      },
      editingDate: null,
      errors: {
        weight: null,
        date: null,
      },
      rules: {
        obligatory: v => !!v || 'Este campo es obligatorio.',
        numeric: value => !isNaN(value) || 'El valor debe ser numérico.',
        date: value => {
          const pattern = /^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/;
          return pattern.test(value) || 'Por favor, ingresá una fecha válida (31/01/2021)'
        },
      }
    }
  },
  computed: {
    initialDate() {
      return this.weight ? this.weight.date : this.getCurrentDate();
    },

    formAction(){
      return this.weight ? 'pesos/editar' : 'pesos/crear'
    },

    buttonText(){
      return this.weight ? "Guardar cambios" : "Agregar"
    },
  },
  mounted() {
    if (this.weight) {
      this.formData.weight = this.weight.weight;
      this.formData.date = this.weight.date;
    }
  },
  methods: {
    getCurrentDate() {
      return (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10);
    },

    updateDate(date) {
      this.formData.date = date;
    },

    sendForm() {
      if (this.$refs.weightForm.validate()) {
        this.loading = true;

        this.errors = {
          date: null,
          weight: null,
        }

        if (!this.weight) {
          weightService.create(this.formData)
              .then(res => {
                this.loading = false;

                if (!res.success) {
                  if (this.handleAccessError(res)) return;

                  if (res.errors && res.errors.pet_id) {
                    this.store.setStatus({
                      msg: 'La mascota no existe.',
                      type: 'error',
                    });
                  } else if (res.errors) {
                    this.errors = {
                      date: null,
                      weight: null,
                      ...res.errors
                    }

                    this.store.setStatus({
                      msg: "Por favor corregí los datos del formulario.",
                      type: 'warning',
                    });
                  } else {
                    this.store.setStatus({
                      msg: 'Algo salió mal. El peso no se guardó correctamente.',
                      type: 'error',
                    });
                  }
                } else {
                  this.store.setStatus({
                    msg: '¡El nuevo peso está guardado!',
                    type: 'success',
                  });

                  this.$router.back();
                }
              })
        } else {
          weightService.update(this.formData, this.weight.id)
              .then(res => {
                this.loading = false;

                if (!res.success) {
                  if (this.handleAccessError(res)) return;

                  if (res.errors && res.errors.pet_id) {
                    this.store.setStatus({
                      msg: 'La mascota no existe.',
                      type: 'error',
                    });
                  } else if (res.errors) {
                    this.errors = {
                      date: null,
                      weight: null,
                      ...res.errors
                    }

                    this.store.setStatus({
                      msg: "Por favor corregí los datos del formulario.",
                      type: 'warning',
                    });
                  } else {
                    this.store.setStatus({
                      msg: 'Algo salió mal. El peso no se editó correctamente.',
                      type: 'error',
                    });
                  }
                } else {
                  this.store.setStatus({
                    msg: '¡Se editó el peso con éxito!',
                    type: 'success',
                  });
                  
                  this.$router.back();
                }
              })
        }
      }
    }
  },
}
</script>
