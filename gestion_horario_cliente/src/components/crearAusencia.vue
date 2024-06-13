<template>
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-600 bg-opacity-50">
      <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Seleccionar Tipo de Ausencia</h3>
          <div class="mt-2 px-7 py-3">
            <div class="flex flex-col md:flex-row justify-center space-y-2 md:space-x-2 md:space-y-0">
              <button @click="selectUnasHoras" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Unas horas</button>
              <button @click="selectTodoElDia" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Todo el día</button>
              <button @click="selectVariosDias" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Varios días</button>
            </div>
            <div v-if="tipoAusencia === 'unasHoras'" class="mt-4">
              <h4 class="text-lg font-medium">Seleccionar Horas</h4>
              <div v-if="horas.length === 0">Cargando horas...</div>
              <div v-else>
                <div v-for="hora in horas" :key="hora" class="flex items-center">
                  <input type="checkbox" :value="hora" v-model="selectedHoras">
                  <label class="ml-2">{{ hora }}</label>
                </div>
              </div>
              <v-date-picker v-model="selectedDate" is-inline />
              <button @click="enviarAusencia('unasHoras')" v-if="selectedHoras.length > 0 && selectedDate" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
            </div>
            <div v-if="tipoAusencia === 'todoElDia'" class="mt-4">
              <h4 class="text-lg font-medium">Seleccionar Día</h4>
              <v-date-picker v-model="selectedDate" is-inline />
              <button @click="enviarAusencia('todoElDia')" v-if="selectedDate" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
            </div>
            <div v-if="tipoAusencia === 'variosDias'" class="mt-4">
              <h4 class="text-lg font-medium">Seleccionar Días</h4>
              <v-date-picker v-model="selectedDates" is-range is-inline />
              <button @click="enviarAusencia('variosDias')" v-if="selectedDates && selectedDates.length === 2" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Enviar</button>
            </div>
          </div>
          <div class="items-center px-4 py-3">
            <button @click="$emit('close')" id="ok-btn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { VDatePicker } from 'v-calendar';
  
  export default {
    name: 'ModalAusencia',
    components: {
      VDatePicker,
    },
    props: {
      showModal: Boolean
    },
    data() {
      return {
        tipoAusencia: '',
        horas: [],
        selectedHoras: [],
        selectedDate: null,
        selectedDates: null,
      };
    },
    methods: {
      async fetchHoras() {
        try {
          const token = sessionStorage.getItem('authToken');
          const response = await fetch('http://127.0.0.1:8080/api/franjas', {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          if (!response.ok) {
            throw new Error(`Failed to fetch horas: ${response.status} ${response.statusText}`);
          }
          const data = await response.json();
          this.horas = data;
        } catch (err) {
          console.error('Error fetching horas:', err);
          this.horas = [];
        }
      },
      selectUnasHoras() {
        this.tipoAusencia = 'unasHoras';
        this.fetchHoras();
      },
      selectTodoElDia() {
        this.tipoAusencia = 'todoElDia';
      },
      selectVariosDias() {
        this.tipoAusencia = 'variosDias';
      },
      async enviarAusencia(tipo) {
        const token = sessionStorage.getItem('authToken');
        let body = {};
        
        if (tipo === 'unasHoras') {
          body = {
            fecha: this.selectedDate,
            horas: this.selectedHoras,
          };
        } else if (tipo === 'todoElDia') {
          body = {
            fecha: this.selectedDate,
          };
        } else if (tipo === 'variosDias') {
          body = {
            fechas: this.selectedDates,
          };
        }
        
        try {
          const response = await fetch('http://127.0.0.1:8080/api/ausencias', {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
          });
          
          if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Failed to create ausencia: ${response.status} ${response.statusText}. Details: ${errorText}`);
          }
          
          // Cerrar modal y recargar la página
          this.$emit('close');
          this.$emit('ausencia-creada');
        } catch (err) {
          console.error('Error creating ausencia:', err);
        }
      }
    }
  }
  </script>
  
  <style scoped>

  </style>
  